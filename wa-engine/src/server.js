import express from 'express';
import cors from 'cors';
import { config } from './config/index.js';
import routes from './routes/index.js';
import { baileysManager } from './services/baileys.manager.js';

const app = express();

app.use(cors());
app.use(express.json({ limit: '50mb' }));
app.use(express.urlencoded({ extended: true, limit: '50mb' }));

// Mount routes
app.use('/engine', routes);

// Global 404
app.use((req, res) => {
  res.status(404).json({
    success: false,
    message: `Route ${req.method} ${req.originalUrl} not found`,
  });
});

// Global Error Handler
app.use((err, req, res, next) => {
  console.error('[Engine Error]', err);
  res.status(500).json({
    success: false,
    message: err.message || 'Internal Server Error',
  });
});

app.listen(config.port, () => {
  console.log(`⚡ Whatsapp Gateway Enterprise Core Engine running on port ${config.port}`);
  console.log(`🔑 Engine Secret Auth: ENABLED`);
  console.log(`📦 Engine: Enterprise Core Engine v1.0.0`);
  
  // Auto restore sessions on startup
  baileysManager.restoreAllSessions();
});
