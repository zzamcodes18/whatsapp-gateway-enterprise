import { Router } from 'express';
import { requireEngineAuth } from '../middleware/auth.middleware.js';
import {
  startSession,
  getSessionStatus,
  logoutSession,
} from '../controllers/session.controller.js';
import {
  sendTextMessage,
  sendMediaMessage,
} from '../controllers/message.controller.js';

const router = Router();

// Health Check
router.get('/health', (req, res) => {
  res.json({
    status: 'ok',
    service: 'Whatsapp Gateway Enterprise Engine',
    engine: '@whiskeysockets/baileys v1.0.0',
    timestamp: new Date().toISOString(),
  });
});

// Protected routes
router.use(requireEngineAuth);

// Session endpoints
router.post('/session/start', startSession);
router.get('/session/:sessionId/status', getSessionStatus);
router.post('/session/:sessionId/logout', logoutSession);

// Message endpoints
router.post('/message/send-text', sendTextMessage);
router.post('/message/send-media', sendMediaMessage);

export default router;
