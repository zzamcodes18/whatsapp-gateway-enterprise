import crypto from 'crypto';
import { config } from '../config/index.js';

const timingSafeCompare = (a, b) => {
  const bufA = Buffer.from(String(a));
  const bufB = Buffer.from(String(b));
  if (bufA.length !== bufB.length) {
    // Bandingkan panjang tetap untuk menghindari kebocoran panjang secret
    return false;
  }
  return crypto.timingSafeEqual(bufA, bufB);
};

export const requireEngineAuth = (req, res, next) => {
  const secret = req.headers['x-engine-secret'];

  // Secret wajib dikonfigurasi & dibandingkan timing-safe (tanpa query string)
  if (!config.engineSecret || !secret || !timingSafeCompare(secret, config.engineSecret)) {
    return res.status(401).json({
      success: false,
      message: 'Unauthorized: Invalid or missing X-Engine-Secret header',
    });
  }

  next();
};
