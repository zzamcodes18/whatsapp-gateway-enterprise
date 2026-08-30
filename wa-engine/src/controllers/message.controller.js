import { baileysManager } from '../services/baileys.manager.js';

export const sendTextMessage = async (req, res) => {
  try {
    const { sessionId, phone, message } = req.body;

    if (!sessionId || !phone || !message) {
      return res.status(400).json({
        success: false,
        message: 'sessionId, phone, and message are required',
      });
    }

    const result = await baileysManager.sendTextMessage(sessionId, phone, message);
    return res.json(result);
  } catch (error) {
    return res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};

export const sendMediaMessage = async (req, res) => {
  try {
    const { sessionId, phone, mediaUrl, mediaType = 'image', caption = '', fileName = 'file.pdf' } = req.body;

    if (!sessionId || !phone || !mediaUrl) {
      return res.status(400).json({
        success: false,
        message: 'sessionId, phone, and mediaUrl are required',
      });
    }

    const result = await baileysManager.sendMediaMessage(sessionId, phone, mediaUrl, mediaType, caption, fileName);
    return res.json(result);
  } catch (error) {
    return res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};

export const sendInteractiveMessage = async (req, res) => {
  try {
    const { sessionId, phone, title, subtitle, body, footer, image, video, document, buttons } = req.body;

    if (!sessionId || !phone) {
      return res.status(400).json({
        success: false,
        message: 'sessionId and phone are required',
      });
    }

    const result = await baileysManager.sendInteractiveMessage(sessionId, phone, {
      title,
      subtitle,
      body,
      footer,
      image,
      video,
      document,
      buttons,
    });
    return res.json(result);
  } catch (error) {
    return res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};
