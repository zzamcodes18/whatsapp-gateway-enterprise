#!/bin/bash

# ==============================================================================
# LAPAKOTP Installer Sub-Module: Main Gateway Panel & Microservice Engine
# Developer: Muhammad Tsaqif Noor Az Zamil (zzamcode)
# ==============================================================================

set -e

install_gateway_panel() {
  print_banner
  log_info "Memulai Modul Instalasi Whatsapp Gateway Enterprise Panel & Engine..."

  prompt_user_inputs

  log_info "1/5. Installing System Dependencies..."
  install_dependencies

  log_info "2/5. Configuring MariaDB / MySQL Database..."
  setup_database

  log_info "3/5. Deploying Gateway Source Code..."
  deploy_source_code

  log_info "4/5. Configuring PM2, Nginx, and System Cron Jobs..."
  setup_services

  log_info "5/5. Finalizing Installation..."
  print_completion
}

prompt_user_inputs() {
  print_divider
  echo -e "${C_BOLD}=== KONFIGURASI INSTALASI PLATFORM ===${C_RESET}"
  print_divider

  read -p "* Masukkan Domain Aplikasi (misal: gateway.domain.com): " INPUT_DOMAIN
  while [ -z "$INPUT_DOMAIN" ]; do
    log_warning "Domain Aplikasi Wajib diisi!"
    read -p "* Masukkan Domain Aplikasi (misal: gateway.domain.com): " INPUT_DOMAIN
  done
  # Clean domain from http:// or https:// if user accidentally inputs it
  DOMAIN_CLEAN=$(echo "$INPUT_DOMAIN" | sed -e 's|^[^/]*//||' -e 's|/.*$||')
  APP_URL="https://${DOMAIN_CLEAN}"

  DB_NAME="wagateway_db"
  DB_USER="wagateway_user"

  read -p "* Masukkan Password Database MySQL: " INPUT_DB_PASS
  while [ -z "$INPUT_DB_PASS" ]; do
    log_warning "Password Database Wajib diisi!"
    read -p "* Masukkan Password Database MySQL: " INPUT_DB_PASS
  done
  DB_PASS=$INPUT_DB_PASS

  WA_ENGINE_SECRET=$(generate_random_secret)

  read -p "* Masukkan Nama Lengkap Master Admin [Master Admin]: " INPUT_ADMIN_NAME
  ADMIN_NAME=${INPUT_ADMIN_NAME:-Master Admin}

  read -p "* Masukkan Email Master Admin [admin@example.com]: " INPUT_ADMIN_EMAIL
  while [ -z "$INPUT_ADMIN_EMAIL" ]; do
    log_warning "Email Master Admin Wajib diisi!"
    read -p "* Masukkan Email Master Admin [admin@example.com]: " INPUT_ADMIN_EMAIL
  done
  ADMIN_EMAIL=$INPUT_ADMIN_EMAIL

  read -p "* Masukkan Password Master Admin [password123]: " INPUT_ADMIN_PASS
  ADMIN_PASS=${INPUT_ADMIN_PASS:-password123}

  read -p "* Masukkan Nomor WhatsApp Admin (opsional, misal 628123456789): " INPUT_ADMIN_PHONE
  ADMIN_PHONE=$INPUT_ADMIN_PHONE

  read -p "* Masukkan Email untuk SSL Certbot [pencet Enter jika samakan dengan email admin]: " INPUT_SSL_EMAIL
  SSL_EMAIL=${INPUT_SSL_EMAIL:-$ADMIN_EMAIL}

  echo ""
  log_info "Ringkasan Parameter Konfigurasi:"
  echo "  - Domain APP       : $APP_URL"
  echo "  - Database Name    : $DB_NAME"
  echo "  - Database User    : $DB_USER"
  echo "  - Target Directory : $INSTALL_DIR"
  echo "  - Admin Name       : $ADMIN_NAME"
  echo "  - Admin Email      : $ADMIN_EMAIL"
  echo "  - Admin Password   : $ADMIN_PASS"
  echo ""
  read -p "Lanjutkan proses instalasi utama? (y/N): " CONFIRM
  if [[ ! "$CONFIRM" =~ ^[Yy]$ ]]; then
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
  mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'%' IDENTIFIED BY '${DB_PASS}';"
  mysql -e "ALTER USER '${DB_USER}'@'%' IDENTIFIED BY '${DB_PASS}';"
  mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'%';"
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
  sed -i "s|WA_ENGINE_SECRET=.*|WA_ENGINE_SECRET=${WA_ENGINE_SECRET}|g" .env

  php artisan key:generate --force

  if [ -f "$INSTALL_DIR/database/schema/database.sql" ]; then
    log_info "Importing Database Schema (database.sql)..."
    mysql "${DB_NAME}" < "$INSTALL_DIR/database/schema/database.sql"
    log_success "Schema database.sql berhasil diimpor!"
  fi

  log_info "Membuat akun Master Admin kustom..."
  php artisan make:admin --name="${ADMIN_NAME}" --email="${ADMIN_EMAIL}" --password="${ADMIN_PASS}" --phone="${ADMIN_PHONE}"

  chown -R www-data:www-data "$INSTALL_DIR"
  chmod -R 775 "$INSTALL_DIR/storage" "$INSTALL_DIR/bootstrap/cache"
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
  echo -e "${C_GREEN}${C_BOLD}================================================================================"
  echo " 🎉 INSTALASI WHATSAPP GATEWAY ENTERPRISE BERHASIL!"
  echo "================================================================================"
  echo -e "${C_RESET}"
  echo " Detail Akses Portal Admin:"
  echo -e "  • URL Portal Dashboard : ${C_CYAN}${APP_URL}${C_RESET}"
  echo -e "  • Email Master Admin   : ${C_YELLOW}${ADMIN_EMAIL}${C_RESET}"
  echo -e "  • Password Master Admin: ${C_YELLOW}password123${C_RESET}"
  echo -e "  • Directory Instalasi  : ${C_CYAN}${INSTALL_DIR}${C_RESET}"
  echo ""
  echo " Status Engine & Database:"
  echo -e "  • Database Name        : ${C_CYAN}${DB_NAME}${C_RESET}"
  echo -e "  • WaEngine Service     : ${C_GREEN}Running via PM2 (Port 3000)${C_RESET}"
  echo -e "  • WaEngine Secret Key  : ${C_YELLOW}${WA_ENGINE_SECRET}${C_RESET}"
  echo "================================================================================"
  echo ""
}
