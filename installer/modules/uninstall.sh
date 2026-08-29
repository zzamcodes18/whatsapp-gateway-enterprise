#!/bin/bash

# ==============================================================================
# Whatsapp Gateway Enterprise Installer Sub-Module: Gateway & Engine Uninstaller
# Developer: Muhammad Tsaqif Noor Az Zamil (zzamcode)
# ==============================================================================

set -e

uninstall_gateway() {
  print_banner
  echo -e "${C_RED}${C_BOLD}=== MODUL UNINSTALLER PLATFORM ===${C_RESET}"
  echo ""
  log_warning "PERINGATAN: Tindakan ini akan menghapus direktori $INSTALL_DIR, service PM2, database, cron job, serta vhost Nginx!"
  read -p "Apakah Anda YAKIN ingin menghapus Whatsapp Gateway Enterprise dari server ini? (y/N): " CONFIRM
  if [[ ! "$CONFIRM" =~ ^[Yy]$ ]]; then
    log_info "Proses uninstall dibatalkan."
    exit 0
  fi

  log_info "1/5. Mematikan & membersihkan service PM2 wa-engine..."
  pm2 delete wa-engine 2>/dev/null || true
  pm2 save 2>/dev/null || true

  log_info "2/5. Menghapus database & user MySQL..."
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
  log_success "Database '${DB_NAME}' dan User '${DB_USER}' berhasil dihapus."

  log_info "3/5. Menghapus konfigurasi Nginx..."
  rm -f /etc/nginx/sites-available/whatsapp-gateway.conf
  rm -f /etc/nginx/sites-enabled/whatsapp-gateway.conf
  rm -f /etc/nginx/sites-available/lapakotp.conf 2>/dev/null || true
  rm -f /etc/nginx/sites-enabled/lapakotp.conf 2>/dev/null || true
  systemctl reload nginx 2>/dev/null || true

  log_info "4/5. Menghapus Cron Job harian..."
  (crontab -l 2>/dev/null | grep -v "gateway:reset-daily-limits") | crontab - || true

  log_info "5/5. Menghapus berkas aplikasi di $INSTALL_DIR..."
  rm -rf "$INSTALL_DIR"

  log_success "Whatsapp Gateway Enterprise telah sukses dicabut (uninstalled) secara bersih dari server."
}
