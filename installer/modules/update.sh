#!/bin/bash

# ==============================================================================
# Whatsapp Gateway Enterprise Sub-Module: Platform Updater
# Developers: Muhammad Zaki (jakisoft) & Muhammad Tsaqif Noor Az Zamil (zzamcode)
# ==============================================================================

set -e

update_gateway() {
  print_banner
  log_info "Memulai Modul Pembaruan (Updater) Whatsapp Gateway Enterprise..."

  if [ ! -d "$INSTALL_DIR" ]; then
    log_error "Platform Whatsapp Gateway Enterprise belum terinstall di $INSTALL_DIR!"
    log_warning "Harap jalankan opsi Instalasi terlebih dahulu."
    exit 1
  fi

  echo -e "${C_BOLD}=== PEMBARUAN PLATFORM (BACKEND & FRONTEND) ===${C_RESET}"
  log_info "Direktori Target: $INSTALL_DIR"
  read -p "* Apakah Anda yakin ingin memperbarui kode ke versi terbaru? (y/N): " CONFIRM
  if [[ ! "$CONFIRM" =~ ^[Yy]$ ]]; then
    log_warning "Pembaruan dibatalkan oleh pengguna."
    exit 0
  fi

  log_info "1/7. Menarik pembaruan kode terbaru dari Git / Repositori..."
  git config --global --add safe.directory "$INSTALL_DIR" 2>/dev/null || true
  git config --global --add safe.directory "*" 2>/dev/null || true
  chown -R root:root "$INSTALL_DIR/.git" 2>/dev/null || true

  cd "$INSTALL_DIR"

  if [ -d "$INSTALL_DIR/.git" ]; then
    git config --local --add safe.directory "$INSTALL_DIR" 2>/dev/null || true
    git pull origin main 2>/dev/null || git pull origin master 2>/dev/null || (git fetch --all && git reset --hard origin/main) 2>/dev/null || true
  else
    log_warning "Direktori $INSTALL_DIR bukan repositori Git. Mengunduh source code dari repositori utama..."
    rm -rf /tmp/lpk_update_tmp 2>/dev/null || true
    git clone "$REPO_URL" /tmp/lpk_update_tmp
    cp -rf /tmp/lpk_update_tmp/* "$INSTALL_DIR"/
    rm -rf /tmp/lpk_update_tmp
  fi

  log_info "2/7. Memperbarui dependensi Composer PHP..."
  export COMPOSER_ALLOW_SUPERUSER=1
  composer install --no-dev --optimize-autoloader --no-interaction --quiet

  log_info "3/7. Memperbarui dependensi Node.js & Membangun ulang Aset Frontend (Vite/Tailwind)..."
  npm install
  npm run build

  log_info "4/7. Memperbarui dependensi Node.js WhatsApp Engine..."
  cd "$INSTALL_DIR/wa-engine"
  npm install
  cd "$INSTALL_DIR"

  log_info "5/7. Menjalankan migrasi database (bila ada tabel/kolom baru)..."
  php artisan migrate --force 2>/dev/null || true

  log_info "6/7. Membersihkan cache aplikasi (Config, Cache, Route, View)..."
  php artisan config:clear
  php artisan cache:clear
  php artisan route:clear
  php artisan view:clear

  log_info "7/7. Memperbarui hak akses folder & merestart service PM2..."
  chown -R www-data:www-data "$INSTALL_DIR"
  chmod -R 775 "$INSTALL_DIR/storage" "$INSTALL_DIR/bootstrap/cache"

  pm2 restart wa-engine 2>/dev/null || pm2 start "$INSTALL_DIR/wa-engine/src/server.js" --name "wa-engine"
  systemctl restart php8.3-fpm 2>/dev/null || true
  systemctl reload nginx 2>/dev/null || true

  echo ""
  echo -e "${C_GREEN}${C_BOLD}================================================================================"
  echo " 🎉 PEMBARUAN (UPDATE) WHATSAPP GATEWAY ENTERPRISE BERHASIL!"
  echo "================================================================================"
  echo -e "${C_RESET}"
  echo -e "  • Versi Terbaru        : ${C_GREEN}Berhasil Diaplikasikan${C_RESET}"
  echo -e "  • Frontend & Assets    : ${C_CYAN}Rebuilt (Vite + Tailwind)${C_RESET}"
  echo -e "  • Service WaEngine     : ${C_GREEN}Restarted & Active${C_RESET}"
  echo "================================================================================"
  echo ""
}
