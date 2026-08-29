import express from 'express';
import cors from 'cors';
import { config } from './config/index.js';
import routes from './routes/index.js';

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
  console.log(`⚡ Whatsapp Gateway Enterprise Baileys Engine running on port ${config.port}`);
  console.log(`🔑 Engine Secret Auth: ENABLED`);
  console.log(`📦 Engine: @whiskeysockets/baileys v7.0.0-rc.14`);
});
