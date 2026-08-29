import path from 'path';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';

dotenv.config();

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export const config = {
  port: process.env.PORT || 3000,
  engineSecret: process.env.ENGINE_SECRET || 'wagateway_secret_key_2026',
  sessionsDir: path.resolve(__dirname, '../../sessions'),
  laravelWebhookUrl: process.env.LARAVEL_WEBHOOK_URL || 'http://127.0.0.1:8000/api/internal/wa-event',
  laravelSecret: process.env.LARAVEL_SECRET || 'wagateway_secret_key_2026',
};
