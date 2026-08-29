# 🚀 Whatsapp Gateway Enterprise - Automated Shell Installer

Skrip installer otomatis interaktif bergaya **Pterodactyl Installer** untuk memasang **Whatsapp Gateway Enterprise** (Laravel 11 + Node.js Baileys v7) secara otomatis di server VPS (Ubuntu 22.04 / 24.04 / Debian 11/12).

---

## ⚡ Cara Penggunaan Instan di VPS

Jalankan perintah satu baris berikut di server VPS Anda (sebagai `root`):

```bash
bash <(curl -sSL https://raw.githubusercontent.com/muhammadtsaqf/whatsapp-gateway/main/installer/install.sh)
```

---

## 🛠️ Otomatisasi yang Dilakukan Installer

1. **Pemeriksaan Hak Akses & OS**: Memvalidasi versi OS Linux (Ubuntu/Debian) dan hak akses `root`.
2. **Instalasi Dependensi Lengkap**:
   - Web Server: Nginx
   - Database: MariaDB / MySQL Server (Otomatis tanpa butuh phpMyAdmin)
   - Runtime PHP: PHP 8.3 & ekstensi lengkap (`fpm`, `mysql`, `curl`, `mbstring`, `xml`, `zip`, `bcmath`)
   - Package Manager: Composer & NPM
   - Node.js Runtime: Node.js v20.x LTS
   - Process Manager: PM2
3. **Pembaruan Otomatis (Update Modul)**:
   - Menarik kode terbaru dari Git (`git pull`)
   - Re-compile aset frontend secara otomatis (`npm run build`)
   - Menjalankan migrasi database baru (`php artisan migrate`)
   - Membersihkan seluruh cache sistem & merestart `wa-engine` PM2 service
4. **Konfigurasi Database Otomatis**: Membuat database `lapakotp_db` dan user `lapakotp_user` secara otomatis tanpa konfigurasi manual.
4. **Deploy Application Core & Microservice**:
   - Install Composer & NPM build frontend
   - Install dependensi WhatsApp Engine (`wa-engine/`)
   - Generate app key & impor schema database (`database/schema/database.sql`)
   - Pengaturan hak akses direktori (`www-data:www-data`)
5. **Konfigurasi Service & Web Server**:
   - Menjalankan `wa-engine` daemon via PM2 service
   - Membuat konfig Virtual Host Nginx otomatis
   - Pemasangan Cron Job untuk auto-reset limit harian (`00:05 WIB`)

---

## 🔐 Kredensial Default Master Admin

Setelah instalasi selesai, Anda dapat langsung masuk ke dashboard portal:

- **URL Dashboard**: `https://domain-anda.com/login`
- **Email Admin**: `admin@lapakotp.com`
- **Password Admin**: `password123`
- **Role & Limit**: Admin Master (**Unlimited Devices & Unlimited Messages**)

---
*Created with ❤️ by zzamcode (Muhammad Tsaqif Noor Az Zamil)*
