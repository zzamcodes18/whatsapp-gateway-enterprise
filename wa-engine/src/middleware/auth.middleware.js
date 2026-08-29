import { config } from '../config/index.js';

export const requireEngineAuth = (req, res, next) => {
  const secret = req.headers['x-engine-secret'] || req.query.secret;

  if (!secret || secret !== config.engineSecret) {
    return res.status(401).json({
      success: false,
      message: 'Unauthorized: Invalid or missing X-Engine-Secret header',
    });
  }

  next();
};
