<p align="center">
  <a href="#" target="_blank">
    <div style="font-size: 32px; font-weight: 800; color: #0F172A; letter-spacing: -1px;">
      WHATSAPP<span style="color: #2563EB;">GATEWAY</span> ENTERPRISE
    </div>
  </a>
</p>

<p align="center">
  <strong>Enterprise WhatsApp Multi-Device Gateway & Developer REST API Engine</strong><br>
  Built with Laravel 11 (PHP 8.3) & Node.js Baileys v7.0.0 Microservice
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP 8.3">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/Engine-Baileys%20v7.0-25D366?style=flat-square&logo=whatsapp&logoColor=white" alt="Baileys v7.0">
  <img src="https://img.shields.io/badge/TailwindCSS-v4.0-38B2AC?style=flat-square&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square" alt="License">
</p>

---

## ⚡ Instalasi Otomatis (One-Line Script)

Anda dapat menginstall seluruh stack platform (Laravel 11, Node.js 20, MariaDB, Nginx, PM2) di VPS Ubuntu/Debian dalam sekali jalan seperti installer Pterodactyl:

```bash
bash <(curl -sSL https://raw.githubusercontent.com/muhammadtsaqf/installer-whatsappgateway/main/install.sh)
```

---

## 🌟 Fitur Utama

- **Dual Connection Mode**: Tautkan nomor WhatsApp menggunakan **Scan QR Code** atau **Pairing Code 8-Digit** tanpa scan kamera.
- **Multi-Device & Multi-User Architecture**: Mendukung banyak sesi perangkat WhatsApp terpisah per akun pengguna (UUID Session).
- **High-Speed REST API v1**: Endpoint pengiriman pesan teks & media berkas (PDF, gambar, dokumen) dengan autentikasi **SHA-256 API Key**.
- **Realtime Webhook Dispatcher**: Kirim notifikasi event otomatis (pesan masuk `message.received`, status koneksi `device.connected` / `device.disconnected`) ke URL server Anda.
- **System Bot Server OTP**: Mode router khusus untuk mengirimkan kode OTP dan notifikasi sistem secara terpusat.
- **Quota Management & Auto-Reset**: Pembatasan jumlah perangkat (`device_limit`) dan kuota pesan harian (`daily_message_limit`) dengan cron reset otomatis setiap pukul 00:05 WIB.
- **Modern Full-Screen Dashboard**: Tampilan console full-screen yang soft, responsif, dan responsif table dengan dukungan horizontal scroll.
- **Fly.io Inspired Auth Experience**: Halaman sign in, sign up, dan reset password bergaya modern dengan performa view transitions yang halus.

---

## 🚀 Panduan Instalasi di VPS (Ubuntu 22.04 / 24.04 / Debian)

### 1. Prasyarat Server
Pastikan VPS Anda telah terpasang:
- **Web Server**: Nginx / Apache
- **PHP**: PHP 8.3 (dengan ekstensi: `php8.3-fpm`, `php8.3-mysql`, `php8.3-curl`, `php8.3-mbstring`, `php8.3-xml`, `php8.3-zip`, `php8.3-bcmath`)
- **Database**: MySQL 8.0+ atau MariaDB 10.4+
- **Node.js**: Node.js v20.x+ & NPM
- **Composer**: Composer 2.x+
- **Process Manager**: PM2 atau Supervisor

### 2. Langkah Clone & Setup File
Masuk ke direktori web server Anda (misal `/var/www/lapakotp`):

```bash
# Clone repository
git clone https://github.com/username/lapakotp.git /var/www/lapakotp
cd /var/www/lapakotp

# Salin file environment
cp .env.example .env

# Install dependensi PHP
composer install --no-dev --optimize-autoloader

# Install dependensi Node.js & compile assets
npm install
npm run build

# Generate Application Encryption Key
php artisan key:generate
```

### 3. Konfigurasi Database di `.env`
Buka file `.env` dan sesuaikan koneksi database MySQL Anda:

```env
APP_NAME="LAPAKOTP Gateway"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://gateway.domain-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lapakotp_db
DB_USERNAME=lapakotp_user
DB_PASSWORD=PasswordDatabaseKuat123!

# URL & Secret Microservice Engine Baileys
WA_ENGINE_URL=http://127.0.0.1:3000
WA_ENGINE_SECRET=lapakotp_secret_key_change_me
```

---

## 🗄️ Panduan Import Database SQL

Tersedia file database siap pakai di: **`database/schema/database.sql`**

### Opsi A: Import Menggunakan phpMyAdmin
1. Buka dashboard **phpMyAdmin** di browser Anda.
2. Klik tombol **New** / **Baru** pada sidebar kiri dan buat database baru bernama `lapakotp_db` dengan Collation `utf8mb4_unicode_ci`.
3. Klik database `lapakotp_db` yang baru dibuat, lalu pilih tab **Import** di menu atas.
4. Klik **Choose File** / **Pilih Berkas**, lalu pilih file **`database/schema/database.sql`** dari folder proyek.
5. Klik tombol **Import** / **Go** di bagian bawah halaman. Seluruh tabel dan data awal admin akan otomatis terpasang.

### Opsi B: Import Melalui Terminal VPS (MySQL CLI)
```bash
# Masuk ke MySQL console dan buat database
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS lapakotp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import file SQL langsung ke database
mysql -u root -p lapakotp_db < /var/www/lapakotp/database/schema/database.sql
```

### Opsi C: Menjalankan Migration & Seeder Bawaan Laravel
```bash
php artisan migrate --force
php artisan db:seed --force
```

---

## 🔑 Akun Default Login

Setelah database diimport/diseed, gunakan akun default berikut untuk login ke console:

| Akun | Email | Password | Role | Hak Akses |
| :--- | :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@lapakotp.com` | `password123` | `admin` | Full Control, Limit Unlimited, Atur Bot Server & Kuota Pengguna |
| **Demo User** | `demo@lapakotp.com` | `password123` | `user` | Mengelola Device Pribadi, Kirim Pesan, Webhooks & API Keys |

> ⚠️ **PENTING**: Segera ubah kata sandi default setelah berhasil masuk pertama kali di menu console.

---

## ⚙️ Konfigurasi Nginx Web Server

Buat file konfigurasi virtual host di `/etc/nginx/sites-available/lapakotp.conf`:

```nginx
server {
    listen 80;
    server_name gateway.domain-anda.com;
    root /var/www/lapakotp/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php index.html;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan konfigurasi dan restart Nginx:
```bash
ln -s /etc/nginx/sites-available/lapakotp.conf /etc/nginx/sites-enabled/
nginx -t
systemctl restart nginx
```

---

## 🤖 Menjalankan Background Worker & Baileys Engine

### Menjalankan Engine WhatsApp dengan PM2:
```bash
# Install PM2 secara global jika belum ada
npm install -g pm2

# Jalankan engine dari direktori engine (jika ada file engine.js / server.js)
pm2 start artisan --name "lapakotp-queue" -- queue:work --sleep=3 --tries=3
pm2 save
pm2 startup
```

### Konfigurasi Cron Otomatis Reset Kuota Harian (00:05 WIB):
Tambahkan entri cronjob Laravel di VPS:
```bash
crontab -e
```
Tambahkan baris berikut di baris paling bawah:
```cron
* * * * * cd /var/www/lapakotp && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📡 Dokumentasi REST API

### 1. Kirim Pesan Teks
- **URL**: `POST /api/v1/messages/send-text`
- **Headers**:
  - `X-API-Key`: `lpk_live_xxxxxxxxxxxx`
  - `Content-Type`: `application/json`

**Contoh Request Payload**:
```json
{
  "device_id": 1,
  "phone": "6281234567890",
  "message": "Kode OTP Anda: 902-192. Berlaku 5 menit."
}
```

**Contoh Response**:
```json
{
  "success": true,
  "message": "Pesan berhasil dijadwalkan.",
  "data": {
    "message_id": 105,
    "status": "sent",
    "wa_message_id": "3EB09F21849182AB"
  }
}
```

### 2. Kirim Pesan Media (Gambar / Dokumen PDF)
- **URL**: `POST /api/v1/messages/send-media`
- **Headers**:
  - `X-API-Key`: `lpk_live_xxxxxxxxxxxx`
  - `Content-Type`: `application/json`

**Contoh Request Payload**:
```json
{
  "device_id": 1,
  "phone": "6281234567890",
  "media_type": "document",
  "media_url": "https://domain-anda.com/invoices/INV-2026-001.pdf",
  "caption": "Terlampir invoice pesanan Anda #INV-2026-001."
}
```

---

## 📄 Lisensi
Platform ini didistribusikan di bawah lisensi [MIT License](LICENSE). Hak Cipta dilindungi LAPAKOTP Gateway.
