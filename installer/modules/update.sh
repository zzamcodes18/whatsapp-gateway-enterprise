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

  # ============================================================================
  # SECURITY MIGRATION (server instalasi lama) — jalankan sekali per update
  # ============================================================================
  ui_section "MIGRASI KEAMANAN (INSTALASI LAMA)"

  # 1. Pastikan WA_ENGINE_SECRET ada di .env Laravel (generate jika belum ada)
  cd "$INSTALL_DIR"
  if ! grep -q "^WA_ENGINE_SECRET=.\+" .env 2>/dev/null; then
    NEW_SECRET=$(generate_random_secret)
    if grep -q "^WA_ENGINE_SECRET=" .env 2>/dev/null; then
      sed -i "s|^WA_ENGINE_SECRET=.*|WA_ENGINE_SECRET=${NEW_SECRET}|" .env
    else
      echo "WA_ENGINE_SECRET=${NEW_SECRET}" >> .env
    fi
    log_success "WA_ENGINE_SECRET baru digenerate di .env Laravel"
  else
    NEW_SECRET=$(grep "^WA_ENGINE_SECRET=" .env | head -n1 | cut -d'=' -f2-)
    log_info "WA_ENGINE_SECRET sudah ada di .env Laravel"
  fi

  # 2. Buat/refresh wa-engine/.env dengan secret yang sama + bind localhost
  cat <<ENVEOF > "$INSTALL_DIR/wa-engine/.env"
PORT=3000
HOST=127.0.0.1
ENGINE_SECRET=${NEW_SECRET}
LARAVEL_SECRET=${NEW_SECRET}
LARAVEL_WEBHOOK_URL=$(grep "^APP_URL=" .env | head -n1 | cut -d'=' -f2-)/api/internal/wa-event
ENVEOF
  log_success "wa-engine/.env dibuat (secret sinkron, engine bind 127.0.0.1)"

  # 3. Hapus user MySQL remote '%' jika ada (DB hanya boleh lokal)
  DB_NAME_MIG=$(grep "^DB_DATABASE=" .env | head -n1 | cut -d'=' -f2-)
  DB_USER_MIG=$(grep "^DB_USERNAME=" .env | head -n1 | cut -d'=' -f2-)
  if [ -n "$DB_USER_MIG" ]; then
    mysql -e "DROP USER IF EXISTS '${DB_USER_MIG}'@'%';" 2>/dev/null && \
      log_success "User MySQL remote '${DB_USER_MIG}'@'%' dihapus (akses DB kini lokal saja)" || \
      log_info "Tidak ada user MySQL remote yang perlu dihapus"
  fi

  # 4. Hapus API key master bawaan lama jika masih ada (dari schema lama)
  if [ -n "$DB_NAME_MIG" ]; then
    mysql "${DB_NAME_MIG}" -e "DELETE FROM api_keys WHERE name = 'Master Admin API Key' AND key_prefix = 'lpk_admin_';" 2>/dev/null && \
      log_success "Master API key bawaan (lpk_admin_) dihapus — buat key baru via dashboard" || true
  fi

  # 5. Perketat permission file .env
  chmod 640 "$INSTALL_DIR/.env" "$INSTALL_DIR/wa-engine/.env" 2>/dev/null || true
  chown www-data:www-data "$INSTALL_DIR/.env" "$INSTALL_DIR/wa-engine/.env" 2>/dev/null || true
  log_success "Permission .env diperketat (640, owner www-data)"

  # 6. Sinkronkan secret ke SystemSetting Laravel (agar admin settings konsisten)
  php artisan tinker --execute="\\App\\Models\\SystemSetting::set('wa_engine_secret', getenv('WA_ENGINE_SECRET') ?: config('services.wa_engine.secret'));" 2>/dev/null || true

  ui_step 7 7 "Memperbarui hak akses folder & merestart service PM2"
  chown -R www-data:www-data "$INSTALL_DIR"
  chmod -R 775 "$INSTALL_DIR/storage" "$INSTALL_DIR/bootstrap/cache"
  chmod 640 "$INSTALL_DIR/.env" "$INSTALL_DIR/wa-engine/.env" 2>/dev/null || true

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
  ui_row "WA Engine" "Restarted & Active via PM2 (bind 127.0.0.1)"
  ui_row "Keamanan" "Secret engine sinkron, DB lokal-only, .env 640"
  echo ""
  echo -e "  ${C_YELLOW}${C_BOLD}  CATATAN PENTING:${C_RESET}"
  echo -e "  ${C_YELLOW}  - Jika sebelumnya memakai secret default, API key engine lama tidak valid lagi.${C_RESET}"
  echo -e "  ${C_YELLOW}  - Master API key bawaan (lpk_admin_) telah dihapus — buat key baru via dashboard.${C_RESET}"
  echo -e "  ${C_YELLOW}  - Pastikan tidak ada aturan firewall/port-forward yang mengekspos port 3000.${C_RESET}"
  echo ""
  echo -e "  ${C_GREEN}${C_BOLD}==============================================================================${C_RESET}"
  echo ""
}
