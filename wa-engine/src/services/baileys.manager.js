import makeWASocket, {
  DisconnectReason,
  useMultiFileAuthState,
  makeCacheableSignalKeyStore,
  Browsers,
  delay,
  fetchLatestBaileysVersion,
} from '@whiskeysockets/baileys';
import pino from 'pino';
import path from 'path';
import fs from 'fs';
import QRCode from 'qrcode';
import axios from 'axios';
import { config } from '../config/index.js';
import { Button } from './button.builder.js';

class BaileysManager {
  constructor() {
    this.sessions = new Map(); // sessionId -> { sock, qr, qrBase64, pairingCode, status, info, method, phoneNumber }
    this.logger = pino({ level: process.env.LOG_LEVEL || 'info' });
  }

  getSessionPath(sessionId) {
    const cleanId = sessionId.replace(/[^a-zA-Z0-9_-]/g, '_');
    return path.join(config.sessionsDir, cleanId);
  }

  async notifyLaravel(event, data) {
    try {
      if (!config.laravelWebhookUrl) return;
      await axios.post(
        config.laravelWebhookUrl,
        {
          event,
          data,
          timestamp: new Date().toISOString(),
        },
        {
          headers: {
            'Content-Type': 'application/json',
            'X-Engine-Secret': config.laravelSecret,
          },
          timeout: 5000,
        }
      );
    } catch (error) {
      this.logger.warn(`Failed to send event ${event} to Laravel: ${error.message}`);
    }
  }

  async initSession(sessionId, options = {}) {
    const { method = 'qr', phoneNumber = null, forceRestart = false } = options;
    const sessionDir = this.getSessionPath(sessionId);

    if (this.sessions.has(sessionId) && !forceRestart) {
      const existing = this.sessions.get(sessionId);
      return {
        sessionId,
        status: existing.status,
        qr: existing.qrBase64,
        pairingCode: existing.pairingCode,
        info: existing.info,
      };
    }

    if (!fs.existsSync(sessionDir)) {
      fs.mkdirSync(sessionDir, { recursive: true });
    }

    const { state, saveCreds } = await useMultiFileAuthState(sessionDir);
    const { version, isLatest } = await fetchLatestBaileysVersion().catch(() => ({
      version: [2, 3000, 1015901307],
      isLatest: true,
    }));

    const sessionData = {
      sessionId,
      sock: null,
      qr: null,
      qrBase64: null,
      pairingCode: null,
      status: 'connecting',
      info: null,
      method,
      phoneNumber,
    };

    this.sessions.set(sessionId, sessionData);

    const sock = makeWASocket({
      version,
      logger: pino({ level: 'silent' }),
      printQRInTerminal: false,
      auth: {
        creds: state.creds,
        keys: makeCacheableSignalKeyStore(state.keys, pino({ level: 'silent' })),
      },
      browser: Browsers.macOS('Safari'),
      generateHighQualityLinkPreview: true,
      syncFullHistory: false,
      markOnlineOnConnect: true,
    });

    sessionData.sock = sock;

    // Handle pairing code if method is pairing_code and not yet registered
    if (method === 'pairing_code' && phoneNumber && !state.creds?.registered) {
      setTimeout(async () => {
        try {
          const cleanNumber = phoneNumber.replace(/[^0-9]/g, '');
          this.logger.info(`Requesting pairing code for session ${sessionId} with phone ${cleanNumber}`);
          const code = await sock.requestPairingCode(cleanNumber);
          sessionData.pairingCode = code;
          sessionData.status = 'pairing_ready';
          this.logger.info(`Pairing code generated for ${sessionId}: ${code}`);
          
          await this.notifyLaravel('session.pairing_code', {
            sessionId,
            pairingCode: code,
            phoneNumber: cleanNumber,
          });
        } catch (err) {
          this.logger.error(`Error requesting pairing code: ${err.message}`);
          sessionData.status = 'error';
          sessionData.error = err.message;
        }
      }, 3000);
    }

    // Credentials update
    sock.ev.on('creds.update', saveCreds);

    // Connection update
    sock.ev.on('connection.update', async (update) => {
      const { connection, lastDisconnect, qr } = update;

      if (qr && method === 'qr') {
        sessionData.qr = qr;
        try {
          sessionData.qrBase64 = await QRCode.toDataURL(qr, {
            margin: 2,
            scale: 8,
            color: {
              dark: '#0A0A0A',
              light: '#FFFFFF',
            },
          });
          sessionData.status = 'qr_ready';
          this.logger.info(`QR Code generated for session ${sessionId}`);

          await this.notifyLaravel('session.qr', {
            sessionId,
            qrCode: sessionData.qrBase64,
          });
        } catch (err) {
          this.logger.error(`QR Code generation error: ${err.message}`);
        }
      }

      if (connection === 'close') {
        const statusCode = lastDisconnect?.error?.output?.statusCode;
        const shouldReconnect = statusCode !== DisconnectReason.loggedOut;

        sessionData.status = 'disconnected';
        sessionData.qr = null;
        sessionData.qrBase64 = null;
        sessionData.pairingCode = null;

        this.logger.warn(
          `Session ${sessionId} closed due to ${lastDisconnect?.error?.message} (status: ${statusCode}). Reconnect: ${shouldReconnect}`
        );

        await this.notifyLaravel('session.disconnected', {
          sessionId,
          reason: lastDisconnect?.error?.message || 'Disconnected',
          statusCode,
        });

        if (shouldReconnect) {
          this.logger.info(`Attempting reconnect for session ${sessionId}...`);
          setTimeout(() => {
            this.initSession(sessionId, { method, phoneNumber, forceRestart: true });
          }, 5000);
        } else {
          // Logged out completely - clean up session directory
          this.deleteSessionStorage(sessionId);
          this.sessions.delete(sessionId);
        }
      } else if (connection === 'open') {
        sessionData.status = 'connected';
        sessionData.qr = null;
        sessionData.qrBase64 = null;
        sessionData.pairingCode = null;

        const user = sock.user;
        sessionData.info = {
          id: user?.id,
          name: user?.name,
          phone: user?.id ? user.id.split(':')[0] : null,
          platform: 'Enterprise Multi-Device v1.0.0',
        };

        this.logger.info(`Session ${sessionId} successfully connected as ${user?.name || user?.id}`);

        await this.notifyLaravel('session.connected', {
          sessionId,
          user: sessionData.info,
        });
      }
    });

    // Inbound Messages
    sock.ev.on('messages.upsert', async ({ messages, type }) => {
      if (type !== 'notify') return;

      for (const msg of messages) {
        if (!msg.message || msg.key.fromMe) continue;

        const remoteJid = msg.key.remoteJid;
        const messageText =
          msg.message.conversation ||
          msg.message.extendedTextMessage?.text ||
          msg.message.imageMessage?.caption ||
          msg.message.documentMessage?.caption ||
          msg.message.videoMessage?.caption ||
          '';

        let messageType = 'text';
        if (msg.message.imageMessage) messageType = 'image';
        else if (msg.message.documentMessage) messageType = 'document';
        else if (msg.message.videoMessage) messageType = 'video';
        else if (msg.message.audioMessage) messageType = 'audio';

        this.logger.info(`Incoming message on session ${sessionId} from ${remoteJid}: ${messageText.slice(0, 50)}`);

        await this.notifyLaravel('message.incoming', {
          sessionId,
          remoteJid,
          messageId: msg.key.id,
          pushName: msg.pushName || '',
          messageType,
          content: messageText,
          timestamp: msg.messageTimestamp,
        });
      }
    });

    return {
      sessionId,
      status: sessionData.status,
      qr: sessionData.qrBase64,
      pairingCode: sessionData.pairingCode,
      info: sessionData.info,
    };
  }

  getSession(sessionId) {
    if (!this.sessions.has(sessionId)) return null;
    const session = this.sessions.get(sessionId);
    return {
      sessionId,
      status: session.status,
      qr: session.qrBase64,
      pairingCode: session.pairingCode,
      info: session.info,
      phoneNumber: session.phoneNumber,
    };
  }

  async sendTextMessage(sessionId, targetPhone, text) {
    const session = this.sessions.get(sessionId);
    if (!session || session.status !== 'connected' || !session.sock) {
      throw new Error(`Device session '${sessionId}' is not connected`);
    }

    const cleanPhone = targetPhone.replace(/[^0-9]/g, '');
    const jid = cleanPhone.includes('@s.whatsapp.net') || cleanPhone.includes('@g.us')
      ? cleanPhone
      : `${cleanPhone}@s.whatsapp.net`;

    const result = await session.sock.sendMessage(jid, { text });
    return {
      success: true,
      messageId: result?.key?.id,
      remoteJid: jid,
      timestamp: result?.messageTimestamp,
    };
  }

  async sendMediaMessage(sessionId, targetPhone, mediaUrl, mediaType = 'image', caption = '', fileName = 'document.pdf') {
    const session = this.sessions.get(sessionId);
    if (!session || session.status !== 'connected' || !session.sock) {
      throw new Error(`Device session '${sessionId}' is not connected`);
    }

    const cleanPhone = targetPhone.replace(/[^0-9]/g, '');
    const jid = cleanPhone.includes('@s.whatsapp.net') || cleanPhone.includes('@g.us')
      ? cleanPhone
      : `${cleanPhone}@s.whatsapp.net`;

    let payload = {};
    if (mediaType === 'image') {
      payload = { image: { url: mediaUrl }, caption };
    } else if (mediaType === 'document') {
      payload = { document: { url: mediaUrl }, caption, fileName, mimetype: 'application/pdf' };
    } else if (mediaType === 'video') {
      payload = { video: { url: mediaUrl }, caption };
    } else if (mediaType === 'audio') {
      payload = { audio: { url: mediaUrl }, mimetype: 'audio/mp4' };
    } else {
      payload = { text: `${caption} \n${mediaUrl}` };
    }

    const result = await session.sock.sendMessage(jid, payload);
    return {
      success: true,
      messageId: result?.key?.id,
      remoteJid: jid,
      timestamp: result?.messageTimestamp,
    };
  }

  async sendInteractiveMessage(sessionId, targetPhone, options) {
    const session = this.sessions.get(sessionId);
    if (!session || session.status !== 'connected' || !session.sock) {
      throw new Error(`Device session '${sessionId}' is not connected`);
    }

    const cleanPhone = targetPhone.replace(/[^0-9]/g, '');
    const jid = cleanPhone.includes('@s.whatsapp.net') || cleanPhone.includes('@g.us')
      ? cleanPhone
      : `${cleanPhone}@s.whatsapp.net`;

    const btn = new Button(session.sock);

    if (options.body) btn.setBody(options.body);
    if (options.footer) btn.setFooter(options.footer);
    if (options.title) btn.setTitle(options.title);
    if (options.subtitle) btn.setSubtitle(options.subtitle);

    if (options.image) btn.setImage(options.image);
    else if (options.video) btn.setVideo(options.video);
    else if (options.document) btn.setDocument(options.document);

    if (Array.isArray(options.buttons)) {
      for (const b of options.buttons) {
        if (b.type === 'reply' || b.name === 'quick_reply') {
          btn.addReply(b.text || b.display_text, b.id || b.displayText || '');
        } else if (b.type === 'url' || b.name === 'cta_url') {
          btn.addUrl(b.text || b.display_text, b.url || '');
        } else if (b.type === 'copy' || b.name === 'cta_copy') {
          btn.addCopy(b.text || b.display_text, b.code || b.copy_code || '');
        } else if (b.type === 'call' || b.name === 'cta_call') {
          btn.addCall(b.text || b.display_text, b.phone || b.id || '');
        } else if (b.type === 'select' || b.name === 'single_select') {
          btn.addSelection(b.title || 'Pilihan');
          if (Array.isArray(b.sections)) {
            for (const sec of b.sections) {
              btn.makeSection(sec.title || '', sec.highlight_label || '');
              if (Array.isArray(sec.rows)) {
                for (const row of sec.rows) {
                  btn.makeRow(row.header || '', row.title || '', row.description || '', row.id || '');
                }
              }
            }
          }
        }
      }
    }

    const msg = await btn.send(jid);

    return {
      success: true,
      messageId: msg.key.id,
      remoteJid: jid,
      timestamp: msg.messageTimestamp,
    };
  }

  async logoutSession(sessionId) {
    const session = this.sessions.get(sessionId);
    if (session && session.sock) {
      try {
        await session.sock.logout();
      } catch (err) {
        this.logger.warn(`Error during socket logout: ${err.message}`);
      }
      try {
        session.sock.end(undefined);
      } catch (err) {
        // ignore
      }
    }

    this.sessions.delete(sessionId);
    this.deleteSessionStorage(sessionId);

    return {
      success: true,
      message: `Session ${sessionId} logged out and credentials deleted`,
    };
  }

  deleteSessionStorage(sessionId) {
    const sessionDir = this.getSessionPath(sessionId);
    if (fs.existsSync(sessionDir)) {
      fs.rmSync(sessionDir, { recursive: true, force: true });
    }
  }
}

export const baileysManager = new BaileysManager();
