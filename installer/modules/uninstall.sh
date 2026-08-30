#!/bin/bash

# ==============================================================================
# WHATSAPP GATEWAY ENTERPRISE — Sub-Module: Platform Uninstaller
# Developers: Muhammad Zaki (jakisoft) & Muhammad Tsaqif Noor Az Zamil (zzamcode)
# ==============================================================================

set -e

uninstall_gateway() {
  print_banner
  ui_section "MODUL UNINSTALLER PLATFORM"
  echo ""
  log_warning "PERINGATAN: Tindakan ini akan menghapus:"
  echo -e "    ${C_DIM}${SYM_BULLET} Direktori aplikasi   : ${INSTALL_DIR}${C_RESET}"
  echo -e "    ${C_DIM}${SYM_BULLET} Service PM2          : wa-engine${C_RESET}"
  echo -e "    ${C_DIM}${SYM_BULLET} Database & user MySQL${C_RESET}"
  echo -e "    ${C_DIM}${SYM_BULLET} Cron job harian${C_RESET}"
  echo -e "    ${C_DIM}${SYM_BULLET} Vhost Nginx${C_RESET}"
  echo ""

  prompt_confirm "Apakah Anda YAKIN ingin menghapus WhatsApp Gateway Enterprise dari server ini?" "N"
  if [[ ! "$PROMPT_RESULT" =~ ^[Yy]$ ]]; then
    log_info "Proses uninstall dibatalkan."
    exit 0
  fi

  prompt_confirm "Konfirmasi terakhir — data yang terhapus TIDAK dapat dikembalikan. Lanjutkan?" "N"
  if [[ ! "$PROMPT_RESULT" =~ ^[Yy]$ ]]; then
    log_info "Proses uninstall dibatalkan."
    exit 0
  fi

  ui_step 1 5 "Mematikan & membersihkan service PM2 wa-engine"
  pm2 delete wa-engine 2>/dev/null || true
  pm2 save 2>/dev/null || true

  ui_step 2 5 "Menghapus database & user MySQL"
  if [ -f "$INSTALL_DIR/.env" ]; then
    DB_NAME=$(grep "^DB_DATABASE=" "$INSTALL_DIR/.env" | cut -d '=' -f2 | tr -d '"' | tr -d "'" || echo "wagateway_db")
    DB_USER=$(grep "^DB_USERNAME=" "$INSTALL_DIR/.env" | cut -d '=' -f2 | tr -d '"' | tr -d "'" || echo "wagateway_user")
  else
    DB_NAME="wagateway_db"
    DB_USER="wagateway_user"
  fi

  mysql -e "DROP DATABASE IF EXISTS \`${DB_NAME}\`;" 2>/dev/null || true
  mysql -e "DROP USER IF EXISTS '${DB_USER}'@'localhost';" 2>/dev/null || true
  mysql -e "FLUSH PRIVILEGES;" 2>/dev/null || true
  log_success "Database '${DB_NAME}' dan user '${DB_USER}' berhasil dihapus."

  ui_step 3 5 "Menghapus konfigurasi Nginx"
  rm -f /etc/nginx/sites-available/whatsapp-gateway.conf
  rm -f /etc/nginx/sites-enabled/whatsapp-gateway.conf
  rm -f /etc/nginx/sites-available/lapakotp.conf 2>/dev/null || true
  rm -f /etc/nginx/sites-enabled/lapakotp.conf 2>/dev/null || true
  systemctl reload nginx 2>/dev/null || true

  ui_step 4 5 "Menghapus cron job harian"
  (crontab -l 2>/dev/null | grep -v "gateway:reset-daily-limits") | crontab - || true

  ui_step 5 5 "Menghapus berkas aplikasi di ${INSTALL_DIR}"
  rm -rf "$INSTALL_DIR"

  echo ""
  echo -e "  ${C_GREEN}${C_BOLD}==============================================================================${C_RESET}"
  echo -e "  ${C_GREEN}${C_BOLD}  ✔   WHATSAPP GATEWAY ENTERPRISE BERHASIL DIUNINSTALL${C_RESET}"
  echo -e "  ${C_GREEN}${C_BOLD}==============================================================================${C_RESET}"
  echo ""
  log_success "Seluruh komponen telah dibersihkan secara menyeluruh dari server."
  echo ""
}
