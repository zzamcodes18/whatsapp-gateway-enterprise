#!/bin/bash

# ==============================================================================
# Whatsapp Gateway Enterprise Installer
# Main Interactive CLI Launcher (Pterodactyl Architecture Inspired)
# Developers: Muhammad Zaki (jakisoft) & Muhammad Tsaqif Noor Az Zamil (zzamcode)
# ==============================================================================

set -e

# Base Environment & Config
export GITHUB_SOURCE="main"
export SCRIPT_RELEASE="v1.0.0"
export GITHUB_BASE_URL="https://raw.githubusercontent.com/muhammadtsaqf/whatsapp-gateway/main/installer"
export INSTALL_DIR="/var/www/whatsapp-gateway"
export REPO_URL="https://github.com/muhammadtsaqf/whatsapp-gateway.git"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Load Core Library
if [ -f "$SCRIPT_DIR/lib/lib.sh" ]; then
  source "$SCRIPT_DIR/lib/lib.sh"
else
  [ -f /tmp/lib.sh ] && rm -rf /tmp/lib.sh
  curl -sSL -o /tmp/lib.sh "$GITHUB_BASE_URL/$GITHUB_SOURCE/lib/lib.sh"
  source /tmp/lib.sh
fi

# Load Sub-Modules
load_module() {
  local module_name="$1"
  if [ -f "$SCRIPT_DIR/modules/$module_name.sh" ]; then
    source "$SCRIPT_DIR/modules/$module_name.sh"
  else
    local tmp_mod="/tmp/mod_$module_name.sh"
    [ -f "$tmp_mod" ] && rm -rf "$tmp_mod"
    curl -sSL -o "$tmp_mod" "$GITHUB_BASE_URL/$GITHUB_SOURCE/modules/$module_name.sh"
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