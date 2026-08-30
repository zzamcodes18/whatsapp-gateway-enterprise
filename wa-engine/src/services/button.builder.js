import { generateWAMessageFromContent, prepareWAMessageMedia } from '@whiskeysockets/baileys';

class BaseBuilder {
  constructor() {
    this._body = '';
    this._footer = '';
    this._title = '';
    this._subtitle = '';
    this._contextInfo = {};
    this._extraPayload = {};
  }
  setBody(text) {
    this._body = text;
    return this;
  }
  setFooter(text) {
    this._footer = text;
    return this;
  }
  setTitle(text) {
    this._title = text;
    return this;
  }
  setSubtitle(text) {
    this._subtitle = text;
    return this;
  }
  setContextInfo(info) {
    this._contextInfo = info;
    return this;
  }
}

export class Button extends BaseBuilder {
  #sock;

  constructor(sock) {
    super();
    if (!sock) {
      throw new Error('Baileys Socket instance is required');
    }
    this.#sock = sock;

    this._buttons = [];
    this._data = null;
    this._currentSelectionIndex = -1;
    this._currentSectionIndex = -1;
    this._params = {};
  }

  setVideo(path, options = {}) {
    if (!path) throw new Error('Url or buffer needed');
    Buffer.isBuffer(path) ? (this._data = { video: path, ...options }) : (this._data = { video: { url: path }, ...options });
    return this;
  }

  setImage(path, options = {}) {
    if (!path) throw new Error('Url or buffer needed');
    Buffer.isBuffer(path) ? (this._data = { image: path, ...options }) : (this._data = { image: { url: path }, ...options });
    return this;
  }

  setDocument(path, options = {}) {
    if (!path) throw new Error('Url or buffer needed');
    Buffer.isBuffer(path) ? (this._data = { document: path, ...options }) : (this._data = { document: { url: path }, ...options });
    return this;
  }

  setMedia(obj) {
    if (typeof obj !== 'object' || obj === null || Array.isArray(obj)) {
      throw new TypeError('Media must be a plain object');
    }
    this._data = obj;
    return this;
  }

  clearButtons() {
    this._buttons = [];
    return this;
  }

  setParams(obj) {
    this._params = obj;
    return this;
  }

  addButton(name, params) {
    this._buttons.push({
      name,
      buttonParamsJson: typeof params === 'string' ? params : JSON.stringify(params),
    });
    return this;
  }

  makeRow(header = '', title = '', description = '', id = '') {
    if (this._currentSelectionIndex === -1 || this._currentSectionIndex === -1) {
      throw new Error('You need to create a selection and a section first');
    }
    const buttonParams = JSON.parse(this._buttons[this._currentSelectionIndex].buttonParamsJson);
    buttonParams.sections[this._currentSectionIndex].rows.push({ header, title, description, id });
    this._buttons[this._currentSelectionIndex].buttonParamsJson = JSON.stringify(buttonParams);
    return this;
  }

  makeSection(title = '', highlight_label = '') {
    if (this._currentSelectionIndex === -1) {
      throw new Error('You need to create a selection first');
    }
    const buttonParams = JSON.parse(this._buttons[this._currentSelectionIndex].buttonParamsJson);
    buttonParams.sections.push({ title, highlight_label, rows: [] });
    this._currentSectionIndex = buttonParams.sections.length - 1;
    this._buttons[this._currentSelectionIndex].buttonParamsJson = JSON.stringify(buttonParams);
    return this;
  }

  addSelection(title, options = {}) {
    this._buttons.push({ ...options, name: 'single_select', buttonParamsJson: JSON.stringify({ title, sections: [] }) });
    this._currentSelectionIndex = this._buttons.length - 1;
    this._currentSectionIndex = -1;
    return this;
  }

  addReply(display_text = '', id = '', options = {}) {
    this._buttons.push({
      name: 'quick_reply',
      buttonParamsJson: JSON.stringify({
        display_text,
        id,
        ...options,
      }),
    });
    return this;
  }

  addCall(display_text = '', id = '', options = {}) {
    this._buttons.push({
      name: 'cta_call',
      buttonParamsJson: JSON.stringify({
        display_text,
        id,
        ...options,
      }),
    });
    return this;
  }

  addReminder(display_text = '', id = '', options = {}) {
    this._buttons.push({
      name: 'cta_reminder',
      buttonParamsJson: JSON.stringify({
        display_text,
        id,
        ...options,
      }),
    });
    return this;
  }

  addCancelReminder(display_text = '', id = '', options = {}) {
    this._buttons.push({
      name: 'cta_cancel_reminder',
      buttonParamsJson: JSON.stringify({
        display_text,
        id,
        ...options,
      }),
    });
    return this;
  }

  addAddress(display_text = '', id = '', options = {}) {
    this._buttons.push({
      name: 'address_message',
      buttonParamsJson: JSON.stringify({
        display_text,
        id,
        ...options,
      }),
    });
    return this;
  }

  addLocation(options = {}) {
    this._buttons.push({
      name: 'send_location',
      buttonParamsJson: JSON.stringify(options),
    });
    return this;
  }

  addUrl(display_text = '', url = '', webview_interaction = false, options = {}) {
    this._buttons.push({
      ...options,
      name: 'cta_url',
      buttonParamsJson: JSON.stringify({
        display_text,
        url,
        webview_interaction,
        ...options,
      }),
    });
    return this;
  }

  addCopy(display_text = '', copy_code = '', options = {}) {
    this._buttons.push({
      name: 'cta_copy',
      buttonParamsJson: JSON.stringify({
        display_text,
        copy_code,
        ...options,
      }),
    });
    return this;
  }

  static paramsList = {
    limited_time_offer: {
      text: 'string',
      url: 'string',
      copy_code: 'string',
      expiration_time: 'number',
    },
    bottom_sheet: {
      in_thread_buttons_limit: 'number',
      divider_indices: ['number'],
      list_title: 'string',
      button_title: 'string',
    },
    tap_target_configuration: {
      title: 'string',
      description: 'string',
      canonical_url: 'string',
      domain: 'string',
      buttonIndex: 'number',
    },
  };

  async toCard() {
    return {
      body: {
        text: this._body,
      },
      footer: {
        text: this._footer,
      },
      header: {
        title: this._title,
        subtitle: this._subtitle,
        hasMediaAttachment: !!this._data,
        ...(this._data
          ? await prepareWAMessageMedia(this._data, { upload: this.#sock.waUploadToServer }).catch((e) => {
              if (String(e).includes('Invalid media type')) return this._data;
              throw e;
            })
          : {}),
      },
      nativeFlowMessage: {
        messageParamsJson: JSON.stringify(this._params),
        buttons: this._buttons,
      },
    };
  }

  async build(jid, { ...options } = {}) {
    const message = await this.toCard();

    return generateWAMessageFromContent(
      jid,
      {
        ...this._extraPayload,
        interactiveMessage: {
          ...message,
          contextInfo: this._contextInfo,
        },
      },
      { ...options }
    );
  }

  async send(jid, { ...options } = {}) {
    const msg = await this.build(jid, options);

    await this.#sock.relayMessage(msg.key.remoteJid, msg.message, {
      messageId: msg.key.id,
      additionalNodes: [
        {
          tag: 'biz',
          attrs: {},
          content: [
            {
              tag: 'interactive',
              attrs: { type: 'native_flow', v: '1' },
              content: [{ tag: 'native_flow', attrs: { v: '9', name: 'mixed' } }],
            },
          ],
        },
      ],
      ...options,
    });
    return msg;
  }
}
