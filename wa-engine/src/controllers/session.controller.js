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
      features: req.body.features || {},
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
    const session = await baileysManager.ensureSessionConnected(sessionId, 3000);

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

export const stopSession = async (req, res) => {
  try {
    const { sessionId } = req.params;
    const result = await baileysManager.stopSession(sessionId);

    if (!result.success) {
      return res.status(404).json(result);
    }

    return res.json(result);
  } catch (error) {
    return res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};

export const startStoppedSession = async (req, res) => {
  try {
    const { sessionId } = req.params;
    const result = await baileysManager.startStoppedSession(sessionId);

    if (!result.success) {
      return res.status(404).json(result);
    }

    return res.json(result);
  } catch (error) {
    return res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};

export const updateFeatures = async (req, res) => {
  try {
    const { sessionId } = req.params;
    const { alwaysOnline, typingIndicator, autoRead, blockCalls } = req.body;

    const features = {};
    if (typeof alwaysOnline === 'boolean') features.alwaysOnline = alwaysOnline;
    if (typeof typingIndicator === 'boolean') features.typingIndicator = typingIndicator;
    if (typeof autoRead === 'boolean') features.autoRead = autoRead;
    if (typeof blockCalls === 'boolean') features.blockCalls = blockCalls;

    if (Object.keys(features).length === 0) {
      return res.status(400).json({
        success: false,
        message: 'No valid feature flags provided (alwaysOnline, typingIndicator, autoRead, blockCalls)',
      });
    }

    const result = await baileysManager.updateSessionFeatures(sessionId, features);

    if (!result.success) {
      return res.status(404).json(result);
    }

    return res.json(result);
  } catch (error) {
    return res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};

export const getConsoleLogs = async (req, res) => {
  try {
    const limit = parseInt(req.query.limit, 10) || 100;
    const sessionId = req.query.sessionId || null;

    let logs = baileysManager.getConsoleLogs(limit);
    if (sessionId) {
      logs = logs.filter((l) => l.message.includes(`[${sessionId}]`));
    }

    return res.json({
      success: true,
      data: logs,
    });
  } catch (error) {
    return res.status(500).json({
      success: false,
      message: error.message,
    });
  }
};
