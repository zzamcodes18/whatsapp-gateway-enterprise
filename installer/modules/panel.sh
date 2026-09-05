#!/bin/bash

# ==============================================================================
# WHATSAPP GATEWAY ENTERPRISE — Sub-Module: Panel & Engine Installer
# Developers: Muhammad Zaki (jakisoft) & Muhammad Tsaqif Noor Az Zamil (zzamcode)
# ==============================================================================

set -e

install_gateway_panel() {
  print_banner
  log_info "Memulai modul instalasi WhatsApp Gateway Enterprise Panel & Engine..."

  prompt_user_inputs

  ui_step 1 5 "Menginstall dependensi sistem"
  install_dependencies

  ui_step 2 5 "Mengonfigurasi database MariaDB / MySQL"
  setup_database

  ui_step 3 5 "Mendeploy source code gateway"
  deploy_source_code

  ui_step 4 5 "Mengonfigurasi PM2, Nginx & cron jobs"
  setup_services

  ui_step 5 5 "Finalisasi instalasi"
  print_completion
}

prompt_user_inputs() {
  ui_section "KONFIGURASI INSTALASI PLATFORM"
  echo -e "  ${C_DIM}Lengkapi parameter berikut. Tekan Enter untuk menggunakan nilai default.${C_RESET}"
  echo ""

  prompt_input "Masukkan Domain Aplikasi (misal: gateway.domain.com)"
  INPUT_DOMAIN="$PROMPT_RESULT"
  DOMAIN_CLEAN=$(echo "$INPUT_DOMAIN" | sed -e 's|^[^/]*//||' -e 's|/.*$||')
  APP_URL="https://${DOMAIN_CLEAN}"

  DB_NAME="wagateway_db"
  DB_USER="wagateway_user"

  prompt_secret "Masukkan Password Database MySQL"
  while [ -z "$PROMPT_RESULT" ]; do
    log_warning "Password database wajib diisi. Silakan coba lagi."
    prompt_secret "Masukkan Password Database MySQL"
  done
  DB_PASS="$PROMPT_RESULT"

  WA_ENGINE_SECRET=$(generate_random_secret)

  prompt_input "Masukkan Nama Lengkap Master Admin" "Master Admin"
  ADMIN_NAME="$PROMPT_RESULT"

  prompt_input "Masukkan Email Master Admin" "admin@example.com"
  while ! echo "$PROMPT_RESULT" | grep -qE '^[^@]+@[^@]+\.[^@]+$'; do
    log_warning "Format email tidak valid. Silakan coba lagi."
    prompt_input "Masukkan Email Master Admin" "admin@example.com"
  done
  ADMIN_EMAIL="$PROMPT_RESULT"

  prompt_secret "Masukkan Password Master Admin" "password123"
  ADMIN_PASS="$PROMPT_RESULT"
  while [ -z "$ADMIN_PASS" ] || [ "$ADMIN_PASS" = "password123" ] || [ ${#ADMIN_PASS} -lt 8 ]; do
    log_warning "Password admin minimal 8 karakter dan tidak boleh 'password123'. Silakan coba lagi."
    prompt_secret "Masukkan Password Master Admin" "password123"
    ADMIN_PASS="$PROMPT_RESULT"
  done

  prompt_input_opt "Masukkan Nomor WhatsApp Admin (opsional, misal 628123456789)" ""
  ADMIN_PHONE="$PROMPT_RESULT"

  prompt_input_opt "Masukkan Email untuk SSL Certbot (Enter = samakan dengan email admin)" "$ADMIN_EMAIL"
  SSL_EMAIL="$PROMPT_RESULT"

  echo ""
  ui_section "RINGKASAN KONFIGURASI"
  ui_row "Domain APP" "${APP_URL}"
  ui_row "Database Name" "${DB_NAME}"
  ui_row "Database User" "${DB_USER}"
  ui_row "Target Directory" "${INSTALL_DIR}"
  ui_row "Admin Name" "${ADMIN_NAME}"
  ui_row "Admin Email" "${ADMIN_EMAIL}"
  echo ""

  prompt_confirm "Lanjutkan proses instalasi utama?" "N"
  if [[ ! "$PROMPT_RESULT" =~ ^[Yy]$ ]]; then
    log_warning "Instalasi dibatalkan oleh pengguna."
    exit 0
  fi
}

install_dependencies() {
  apt-get update -y
  apt-get install -y software-properties-common curl wget git unzip zip ca-certificates gnupg lsb-release

  add-apt-repository ppa:ondrej/php -y || true
  apt-get update -y

  apt-get install -y php8.3 php8.3-cli php8.3-fpm php8.3-mysql php8.3-curl php8.3-mbstring php8.3-xml php8.3-zip php8.3-bcmath php8.3-intl php8.3-gd

  if ! command -v composer &> /dev/null; then
    log_info "Menginstall Composer..."
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
  fi

  NODE_MAJOR=$(node -v 2>/dev/null | cut -d'v' -f2 | cut -d'.' -f1 || echo "0")
  if [ -z "$NODE_MAJOR" ] || [ "$NODE_MAJOR" -lt 22 ]; then
    log_info "Menginstall / Meng-upgrade Node.js ke versi 24..."
    curl -fsSL https://deb.nodesource.com/setup_24.x | bash -
    apt-get install -y nodejs
  fi

  npm install -g pm2
  apt-get install -y nginx mariadb-server mariadb-client certbot python3-certbot-nginx
  systemctl enable mariadb --now
  systemctl enable nginx --now
  systemctl enable php8.3-fpm --now

  log_success "Seluruh dependensi server berhasil terinstall!"
}

setup_database() {
  mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
  mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASS}';"
  mysql -e "ALTER USER '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASS}';"
  mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'127.0.0.1';"
  mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
  mysql -e "ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
  mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';"
  # Hapus user remote '%' jika ada dari instalasi lama — DB hanya boleh diakses lokal
  mysql -e "DROP USER IF EXISTS '${DB_USER}'@'%';"
  mysql -e "FLUSH PRIVILEGES;"

  log_success "Database '${DB_NAME}' dan User '${DB_USER}' berhasil siap digunakan."
}

deploy_source_code() {
  if [ -d "$INSTALL_DIR" ]; then
    log_warning "Folder ${INSTALL_DIR} ditemukan. Melakukan penimpaan/pembersihan..."
    rm -rf "$INSTALL_DIR"
  fi

  mkdir -p "$INSTALL_DIR"

  SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
  ROOT_PROJECT_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"

  if [ -f "$ROOT_PROJECT_DIR/composer.json" ]; then
    log_info "Menyalin berkas dari repositori lokal..."
    cp -r "$ROOT_PROJECT_DIR"/* "$INSTALL_DIR"/
  else
    log_info "Mengklon repositori dari GitHub (${REPO_URL})..."
    git clone "$REPO_URL" "$INSTALL_DIR"
  fi

  cd "$INSTALL_DIR"

  log_info "Installing PHP Composer Packages..."
  export COMPOSER_ALLOW_SUPERUSER=1
  composer install --no-dev --optimize-autoloader --no-interaction --quiet

  log_info "Installing NPM & Building Frontend Assets..."
  npm install
  npm run build

  log_info "Installing Node.js WhatsApp Engine Dependencies..."
  cd "$INSTALL_DIR/wa-engine"
  npm install
  cd "$INSTALL_DIR"

  cp .env.example .env
  sed -i "s|DB_CONNECTION=.*|DB_CONNECTION=mysql|g" .env
  sed -i "s|DB_HOST=.*|DB_HOST=127.0.0.1|g" .env
  sed -i "s|APP_URL=.*|APP_URL=${APP_URL}|g" .env
  sed -i "s|DB_DATABASE=.*|DB_DATABASE=${DB_NAME}|g" .env
  sed -i "s|DB_USERNAME=.*|DB_USERNAME=${DB_USER}|g" .env
  sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=\"${DB_PASS}\"|g" .env

  # Pastikan baris WA_ENGINE_SECRET ada, lalu isi dengan secret acak
  if ! grep -q "^WA_ENGINE_SECRET=" .env; then
    echo "WA_ENGINE_SECRET=${WA_ENGINE_SECRET}" >> .env
  else
    sed -i "s|^WA_ENGINE_SECRET=.*|WA_ENGINE_SECRET=${WA_ENGINE_SECRET}|" .env
  fi

  # Buat .env untuk wa-engine (secret yang sama, bind localhost)
  cat <<ENVEOF > "$INSTALL_DIR/wa-engine/.env"
PORT=3000
HOST=127.0.0.1
ENGINE_SECRET=${WA_ENGINE_SECRET}
LARAVEL_SECRET=${WA_ENGINE_SECRET}
LARAVEL_WEBHOOK_URL=${APP_URL}/api/internal/wa-event
ENVEOF

  php artisan key:generate --force

  if [ -f "$INSTALL_DIR/database/schema/database.sql" ]; then
    log_info "Importing Database Schema (database.sql)..."
    mysql "${DB_NAME}" < "$INSTALL_DIR/database/schema/database.sql"
    log_success "Schema database.sql berhasil diimpor!"
  fi

  log_info "Membuat akun Master Admin kustom..."
  php artisan make:admin --name="${ADMIN_NAME}" --email="${ADMIN_EMAIL}" --password="${ADMIN_PASS}" --phone="${ADMIN_PHONE}"

  log_info "Menyemai data default (Plan Free & Admin)..."
  php artisan db:seed --class=PlanSeeder --force 2>/dev/null || true

  chown -R www-data:www-data "$INSTALL_DIR"
  chmod -R 775 "$INSTALL_DIR/storage" "$INSTALL_DIR/bootstrap/cache"
  chmod 640 "$INSTALL_DIR/.env" "$INSTALL_DIR/wa-engine/.env"
  chown www-data:www-data "$INSTALL_DIR/.env" "$INSTALL_DIR/wa-engine/.env"
}

setup_services() {
  cd "$INSTALL_DIR/wa-engine"
  pm2 delete wa-engine 2>/dev/null || true
  pm2 start src/server.js --name "wa-engine"
  pm2 save
  pm2 startup | tail -n 1 | bash || true

  cat <<EOF > /etc/nginx/sites-available/whatsapp-gateway.conf
server {
    listen 80;
    server_name ${DOMAIN_CLEAN};
    root ${INSTALL_DIR}/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

  ln -sf /etc/nginx/sites-available/whatsapp-gateway.conf /etc/nginx/sites-enabled/
  rm -f /etc/nginx/sites-enabled/default 2>/dev/null || true
  nginx -t
  systemctl reload nginx

  log_info "Mengonfigurasi SSL Certificate gratis via Certbot..."
  if certbot --nginx --non-interactive --agree-tos --email "${SSL_EMAIL}" -d "${DOMAIN_CLEAN}"; then
    log_success "SSL Certificate Let's Encrypt berhasil dipasang untuk domain ${DOMAIN_CLEAN}!"
  else
    log_warning "Pemasangan SSL Certbot gagal atau DNS domain belum mengarah ke IP VPS ini. Anda dapat mengkonfigurasi SSL nanti dengan: certbot --nginx -d ${DOMAIN_CLEAN}"
  fi

  (crontab -l 2>/dev/null; echo "5 0 * * * cd ${INSTALL_DIR} && php artisan gateway:reset-daily-limits >> /dev/null 2>&1") | crontab -
}

print_completion() {
  echo ""
  echo -e "  ${C_GREEN}${C_BOLD}==============================================================================${C_RESET}"
  echo -e "  ${C_GREEN}${C_BOLD}  ✔   INSTALASI WHATSAPP GATEWAY ENTERPRISE BERHASIL!${C_RESET}"
  echo -e "  ${C_GREEN}${C_BOLD}==============================================================================${C_RESET}"
  echo ""
  echo -e "  ${C_BOLD}  Detail Akses Portal Admin:${C_RESET}"
  ui_row "URL Portal" "${C_CYAN}${APP_URL}${C_RESET}"
  ui_row "Email Admin" "${C_YELLOW}${ADMIN_EMAIL}${C_RESET}"
  ui_row "Password Admin" "${C_YELLOW}${ADMIN_PASS}${C_RESET}"
  ui_row "Directory" "${C_CYAN}${INSTALL_DIR}${C_RESET}"
  echo ""
  echo -e "  ${C_BOLD}  Status Engine & Database:${C_RESET}"
  ui_row "Database" "${C_CYAN}${DB_NAME}${C_RESET}"
  ui_row "WA Engine" "${C_GREEN}Running via PM2 (Port 3000)${C_RESET}"
  ui_row "Secret Key" "${C_YELLOW}${WA_ENGINE_SECRET}${C_RESET}"
  echo ""
  echo -e "  ${C_GREEN}${C_BOLD}==============================================================================${C_RESET}"
  echo ""
}
