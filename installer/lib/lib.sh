#!/bin/bash

# ==============================================================================
# Whatsapp Gateway Enterprise Installer
# Library & Helper Utilities
# Developer: Muhammad Tsaqif Noor Az Zamil (zzamcode)
# ==============================================================================

set -e

# Visual Tokens & Formatting
export C_RESET='\033[0m'
export C_RED='\033[0;31m'
export C_GREEN='\033[0;32m'
export C_YELLOW='\033[1;33m'
export C_BLUE='\033[0;34m'
export C_MAGENTA='\033[0;35m'
export C_CYAN='\033[0;36m'
export C_BOLD='\033[1m'

print_banner() {
  clear
  echo -e "${C_CYAN}${C_BOLD}"
  echo "  ██╗      █████╗ ██████╗  █████╗ ██╗  ██╗██████╗ ████████╗██████╗ "
  echo "  ██║     ██╔══██╗██╔══██╗██╔══██╗██║ ██╔╝██╔══██╗╚══██╔══╝██╔══██╗"
  echo "  ██║     ███████║██████╔╝███████║█████╔╝ ██║  ██║   ██║   ██████╔╝"
  echo "  ██║     ██╔══██║██╔═══╝ ██╔══██║██╔═██╗ ██║  ██║   ██║   ██╔═══╝ "
  echo "  ███████╗██║  ██║██║     ██║  ██║██║  ██╗██████╔╝   ██║   ██║     "
  echo "  ╚══════╝╚═╝  ╚═╝╚═╝     ╚═╝  ╚═╝╚═╝  ╚═╝╚═════╝    ╚═╝   ╚═╝     "
  echo -e "${C_RESET}"
  echo -e "${C_BOLD}     Whatsapp Gateway Enterprise Modular Installer${C_RESET}"
  echo -e "${C_BLUE}           Built by zzamcode (Muhammad Tsaqif Noor Az Zamil)${C_RESET}"
  echo "================================================================================"
  echo ""
}

log_info() {
  echo -e "${C_CYAN}[INFO]${C_RESET} $1"
}

log_success() {
  echo ""
  echo -e "${C_GREEN}[SUCCESS]${C_RESET} $1"
  echo ""
}

log_warning() {
  echo -e "${C_YELLOW}[WARNING]${C_RESET} $1"
}

log_error() {
  echo -e "${C_RED}[ERROR]${C_RESET} $1" 1>&2
}

print_divider() {
  echo "--------------------------------------------------------------------------------"
}

check_root() {
  if [ "$EUID" -ne 0 ]; then
    log_error "Harap jalankan skrip installer ini sebagai root (sudo su / sudo bash)."
    exit 1
  fi
}

check_os() {
  if [ -f /etc/os-release ]; then
    . /etc/os-release
    export OS=$NAME
    export VER=$VERSION_ID
  else
    log_error "Sistem operasi tidak terdeteksi. Gunakan Ubuntu 22.04 / 24.04 atau Debian 11/12."
    exit 1
  fi

  log_info "Sistem Operasi terdeteksi: ${C_BOLD}$OS ($VER)${C_RESET}"
}

generate_random_secret() {
  tr -dc 'a-zA-Z0-9' </dev/urandom | head -c 32
}
