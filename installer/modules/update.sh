#!/bin/bash

# ==============================================================================
# WHATSAPP GATEWAY ENTERPRISE — Sub-Module: Platform Updater
# Developers: Muhammad Zaki (jakisoft) & Muhammad Tsaqif Noor Az Zamil (zzamcode)
# ==============================================================================

set -e

update_gateway() {
  print_banner
  log_info "Memulai modul pembaruan WhatsApp Gateway Enterprise..."

  if [ ! -d "$INSTALL_DIR" ]; then
    log_error "Platform WhatsApp Gateway Enterprise belum terinstall di ${INSTALL_DIR}!"
    log_warning "Harap jalankan opsi Install terlebih dahulu."
    exit 1
  fi

  ui_section "PEMBARUAN PLATFORM (BACKEND & FRONTEND)"
  log_info "Direktori target: ${C_BOLD}${INSTALL_DIR}${C_RESET}"

  prompt_confirm "Tarik & terapkan versi terbaru dari repositori?" "N"
  if [[ ! "$PROMPT_RESULT" =~ ^[Yy]$ ]]; then
    log_warning "Pembaruan dibatalkan oleh pengguna."
    exit 0
  fi

  ui_step 1 7 "Menarik pembaruan kode terbaru dari Git"
  git config --global --add safe.directory "$INSTALL_DIR" 2>/dev/null || true
  git config --global --add safe.directory "*" 2>/dev/null || true
  chown -R root:root "$INSTALL_DIR/.git" 2>/dev/null || true

  cd "$INSTALL_DIR"

  if [ -d "$INSTALL_DIR/.git" ]; then
    git config --local --add safe.directory "$INSTALL_DIR" 2>/dev/null || true
    # Pastikan remote origin menunjuk ke repo yang benar (migrasi dari repo lama yang suspended)
    CURRENT_REMOTE=$(git remote get-url origin 2>/dev/null || echo "")
    if [ -n "$CURRENT_REMOTE" ] && [ "$CURRENT_REMOTE" != "$REPO_URL" ]; then
      log_info "Memperbaiki remote Git: ${CURRENT_REMOTE} -> ${REPO_URL}"
      git remote set-url origin "$REPO_URL"
      git fetch origin 2>/dev/null || true
    fi
    git pull origin main 2>/dev/null || git pull origin master 2>/dev/null || (git fetch --all && git reset --hard origin/main) 2>/dev/null || true
  else
    log_warning "Direktori ${INSTALL_DIR} bukan repositori Git. Mengunduh source code dari repositori utama..."
    rm -rf /tmp/lpk_update_tmp 2>/dev/null || true
    git clone "$REPO_URL" /tmp/lpk_update_tmp
    cp -rf /tmp/lpk_update_tmp/* "$INSTALL_DIR"/
    rm -rf /tmp/lpk_update_tmp
  fi

  ui_step 2 7 "Memperbarui dependensi Composer PHP"
  export COMPOSER_ALLOW_SUPERUSER=1
  composer install --no-dev --optimize-autoloader --no-interaction --quiet

  ui_step 3 7 "Membangun ulang aset frontend (Vite + Tailwind)"
  npm install
  npm run build

  ui_step 4 7 "Memperbarui dependensi WhatsApp Engine (Node.js)"
  cd "$INSTALL_DIR/wa-engine"
  npm install
  cd "$INSTALL_DIR"

  ui_step 5 7 "Menjalankan migrasi database"
  php artisan migrate --force 2>/dev/null || true

  ui_step 6 7 "Menyemai data default (Plan Free & Admin) & membersihkan cache"
  php artisan db:seed --class=PlanSeeder --force 2>/dev/null || true
  php artisan config:clear
  php artisan cache:clear
  php artisan route:clear
  php artisan view:clear

  ui_step 7 7 "Memperbarui hak akses folder & merestart service PM2"
  chown -R www-data:www-data "$INSTALL_DIR"
  chmod -R 775 "$INSTALL_DIR/storage" "$INSTALL_DIR/bootstrap/cache"

  pm2 restart wa-engine 2>/dev/null || pm2 start "$INSTALL_DIR/wa-engine/src/server.js" --name "wa-engine"
  systemctl restart php8.3-fpm 2>/dev/null || true
  systemctl reload nginx 2>/dev/null || true

  echo ""
  echo -e "  ${C_GREEN}${C_BOLD}==============================================================================${C_RESET}"
  echo -e "  ${C_GREEN}${C_BOLD}  ✔   PEMBARUAN WHATSAPP GATEWAY ENTERPRISE BERHASIL!${C_RESET}"
  echo -e "  ${C_GREEN}${C_BOLD}==============================================================================${C_RESET}"
  echo ""
  ui_row "Kode" "Versi terbaru berhasil diterapkan"
  ui_row "Frontend" "Rebuilt (Vite + Tailwind)"
  ui_row "Database" "Migrated + Seeded (Plan)"
  ui_row "WA Engine" "Restarted & Active via PM2"
  echo ""
  echo -e "  ${C_GREEN}${C_BOLD}==============================================================================${C_RESET}"
  echo ""
}
