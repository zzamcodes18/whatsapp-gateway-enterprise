#!/bin/bash

# ==============================================================================
# Whatsapp Gateway Enterprise Installer
# Main Interactive CLI Launcher (Pterodactyl Architecture Inspired)
# Developers: Muhammad Zaki (jakisoft) & Muhammad Tsaqif Noor Az Zamil (zzamcode)
# ==============================================================================

set -e

# Purge any old temporary files
rm -f /tmp/wagateway_lib.sh /tmp/wagateway_mod_*.sh /tmp/lib.sh /tmp/mod_*.sh 2>/dev/null || true

# Base Environment & Config
export GITHUB_SOURCE="main"
export SCRIPT_RELEASE="v1.0.0"
export INSTALL_DIR="/var/www/whatsapp-gateway"
export REPO_URL="https://github.com/muhammadtsaqf/whatsapp-gateway.git"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Load Core Library
if [ -f "$SCRIPT_DIR/lib/lib.sh" ]; then
  source "$SCRIPT_DIR/lib/lib.sh"
else
  CACHE_BUST=$(date +%s)
  LIB_URL="https://raw.githubusercontent.com/muhammadtsaqf/whatsapp-gateway/main/installer/lib/lib.sh?v=${CACHE_BUST}"
  if ! curl -sSLf "$LIB_URL" -o /tmp/wagateway_lib.sh; then
    echo "[ERROR] Gagal mengunduh library installer (lib.sh) dari GitHub."
    echo "URL: $LIB_URL"
    exit 1
  fi
  source /tmp/wagateway_lib.sh
fi

# Load Sub-Modules
load_module() {
  local module_name="$1"
  if [ -f "$SCRIPT_DIR/modules/$module_name.sh" ]; then
    source "$SCRIPT_DIR/modules/$module_name.sh"
  else
    local tmp_mod="/tmp/wagateway_mod_${module_name}.sh"
    rm -f "$tmp_mod" 2>/dev/null || true
    CACHE_BUST=$(date +%s)
    MOD_URL="https://raw.githubusercontent.com/muhammadtsaqf/whatsapp-gateway/main/installer/modules/${module_name}.sh?v=${CACHE_BUST}"
    if ! curl -sSLf "$MOD_URL" -o "$tmp_mod"; then
      echo "[ERROR] Gagal mengunduh modul installer (${module_name}) dari GitHub."
      echo "URL: $MOD_URL"
      exit 1
    fi
    source "$tmp_mod"
  fi
}

main_menu() {
  print_banner
  check_root
  check_os

  echo -e "${C_BOLD}Silakan pilih aksi yang ingin Anda lakukan:${C_RESET}"
  print_divider
  echo -e " ${C_CYAN}[1]${C_RESET} Install Complete Platform (Laravel Panel + Node.js Engine)"
  echo -e " ${C_CYAN}[2]${C_RESET} Update Platform (Auto Pull, Rebuild Frontend & Migrate Backend)"
  echo -e " ${C_CYAN}[3]${C_RESET} Uninstall Platform & Clear Services"
  echo -e " ${C_CYAN}[4]${C_RESET} Keluar (Exit)"
  print_divider
  echo ""

  read -p "Masukkan nomor pilihan Anda [1-4]: " ACTION

  case "$ACTION" in
    1)
      load_module "panel"
      install_gateway_panel
      ;;
    2)
      load_module "update"
      update_gateway
      ;;
    3)
      load_module "uninstall"
      uninstall_gateway
      ;;
    4)
      log_info "Terima kasih telah menggunakan LAPAKOTP Installer."
      exit 0
      ;;
    *)
      log_error "Pilihan tidak valid!"
      exit 1
      ;;
  esac
}

main_menu "$@"