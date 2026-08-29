import { baileysManager } from '../services/baileys.manager.js';

export const startSession = async (req, res) => {
  try {
    const { sessionId, method = 'qr', phoneNumber = null } = req.body;

    if (!sessionId) {
      return res.status(400).json({
        success: false,
        message: 'sessionId is required',
      });
    }

    if (method === 'pairing_code' && !phoneNumber) {
      return res.status(400).json({
        success: false,
        message: 'phoneNumber is required for pairing_code method',
      });
    }

    const session = await baileysManager.initSession(sessionId, {
      method,
      phoneNumber,
      forceRestart: req.body.forceRestart || false,
    });

    return res.json({
      success: true,
      data: session,
    });
  } catch (error) {
    return res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};

export const getSessionStatus = async (req, res) => {
  try {
    const { sessionId } = req.params;
    const session = baileysManager.getSession(sessionId);

    if (!session) {
      return res.status(404).json({
        success: false,
        message: `Session '${sessionId}' not found or inactive`,
      });
    }

    return res.json({
      success: true,
      data: session,
    });
  } catch (error) {
    return res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};

export const logoutSession = async (req, res) => {
  try {
    const { sessionId } = req.params;
    const result = await baileysManager.logoutSession(sessionId);

    return res.json(result);
  } catch (error) {
    return res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};
