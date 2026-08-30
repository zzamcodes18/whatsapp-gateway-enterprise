#!/bin/bash

# ==============================================================================
#  WHATSAPP GATEWAY ENTERPRISE — INSTALLER CORE LIBRARY
#  Professional CLI UI Toolkit & System Helper Utilities
#  Developers: Muhammad Zaki (jakisoft) & Muhammad Tsaqif Noor Az Zamil (zzamcode)
# ==============================================================================

set -e

# ------------------------------------------------------------------------------
#  Color Palette & Visual Tokens
# ------------------------------------------------------------------------------
export C_RESET='\033[0m'
export C_BOLD='\033[1m'
export C_DIM='\033[2m'
export C_RED='\033[0;31m'
export C_GREEN='\033[0;32m'
export C_YELLOW='\033[1;33m'
export C_BLUE='\033[0;34m'
export C_MAGENTA='\033[0;35m'
export C_CYAN='\033[0;36m'
export C_WHITE='\033[1;37m'

# UI Symbols
export SYM_INFO='▸'
export SYM_OK='✔'
export SYM_WARN='⚠'
export SYM_ERR='✖'
export SYM_ARROW='→'
export SYM_BULLET='•'

# ------------------------------------------------------------------------------
#  Banner & Frames
# ------------------------------------------------------------------------------
print_banner() {
  clear
  echo -e "${C_CYAN}${C_BOLD}"
  echo "    ██╗    ██╗ █████╗ ██╗     ██╗      ██████╗  ██████╗ ███████╗██████╗ "
  echo "    ██║    ██║██╔══██╗██║     ██║     ██╔════╝ ██╔═══██╗██╔════╝██╔══██╗"
  echo "    ██║ █╗ ██║███████║██║     ██║     ██║  ███╗██║   ██║█████╗  ██████╔╝"
  echo "    ██║███╗██║██╔══██║██║     ██║     ██║   ██║██║   ██║██╔══╝  ██╔══██╗"
  echo "    ╚███╔███╔╝██║  ██║███████╗███████╗╚██████╔╝╚██████╔╝███████╗██║  ██║"
  echo "     ╚══╝╚══╝ ╚═╝  ╚═╝╚══════╝╚══════╝ ╚═════╝  ╚═════╝ ╚══════╝╚═╝  ╚═╝"
  echo -e "${C_RESET}"
  echo -e "    ${C_DIM}────────────────────────────────────────────────────────────────${C_RESET}"
  echo -e "    ${C_BOLD}W H A T S A P P   G A T E W A Y   E N T E R P R I S E${C_RESET}"
  echo -e "    ${C_DIM}Professional Installer Suite${C_RESET}  ${C_DIM}•${C_RESET}  ${C_BOLD}${SCRIPT_RELEASE:-v1.0.0}${C_RESET}"
  echo -e "    ${C_DIM}by jakisoft (Muhammad Zaki) & zzamcode (Muhammad Tsaqif)${C_RESET}"
  echo -e "    ${C_DIM}────────────────────────────────────────────────────────────────${C_RESET}"
  echo ""
}

print_divider() {
  echo -e "  ${C_DIM}──────────────────────────────────────────────────────────────────${C_RESET}"
}

ui_section() {
  echo ""
  echo -e "  ${C_CYAN}${C_BOLD}┌──[ $1 ]${C_RESET}"
  echo -e "  ${C_DIM}└────────────────────────────────────────────────────────────────${C_RESET}"
}

ui_step() {
  # ui_step <current> <total> <description>
  echo ""
  echo -e "  ${C_BLUE}${C_BOLD} STEP $1/$2 ${C_RESET}${C_DIM}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${C_RESET}"
  echo -e "  ${C_BOLD} $3${C_RESET}"
  echo ""
}

ui_row() {
  # ui_row "Label" "Value"
  printf "  ${C_DIM}%-24s${C_RESET} ${C_BOLD}%s${C_RESET}\n" "$1:" "$2"
}

# ------------------------------------------------------------------------------
#  Logging
# ------------------------------------------------------------------------------
log_info() {
  echo -e "  ${C_CYAN}[${SYM_INFO}]${C_RESET} $1"
}

log_success() {
  echo -e "  ${C_GREEN}[${SYM_OK}]${C_RESET} ${C_BOLD}$1${C_RESET}"
}

log_warning() {
  echo -e "  ${C_YELLOW}[${SYM_WARN}]${C_RESET} $1"
}

log_error() {
  echo -e "  ${C_RED}[${SYM_ERR}]${C_RESET} $1" 1>&2
}

# ------------------------------------------------------------------------------
#  Interactive Prompt Helpers
# ------------------------------------------------------------------------------
prompt_input() {
  # prompt_input <label> [default] — hasil di variable PROMPT_RESULT
  local label="$1" default="${2:-}" answer=""
  if [ -n "$default" ]; then
    read -r -p "$(echo -e "  ${C_CYAN}${SYM_ARROW}${C_RESET} ${C_BOLD}${label}${C_RESET} ${C_DIM}[default: ${default}]${C_RESET} ")" answer
    answer="${answer:-$default}"
  else
    while [ -z "$answer" ]; do
      read -r -p "$(echo -e "  ${C_CYAN}${SYM_ARROW}${C_RESET} ${C_BOLD}${label}${C_RESET} ")" answer
      if [ -z "$answer" ]; then
        log_warning "Input tidak boleh kosong. Silakan coba lagi."
      fi
    done
  fi
  PROMPT_RESULT="$answer"
}

prompt_input_opt() {
  # prompt_input_opt <label> [default] — input opsional (boleh kosong)
  local label="$1" default="${2:-}" answer=""
  read -r -p "$(echo -e "  ${C_CYAN}${SYM_ARROW}${C_RESET} ${C_BOLD}${label}${C_RESET} ${C_DIM}[${default}]${C_RESET} ")" answer
  PROMPT_RESULT="${answer:-$default}"
}

prompt_secret() {
  # prompt_secret <label> [default] — input tersembunyi, hasil di PROMPT_RESULT
  local label="$1" default="${2:-}" answer=""
  read -r -s -p "$(echo -e "  ${C_CYAN}${SYM_ARROW}${C_RESET} ${C_BOLD}${label}${C_RESET} ${C_DIM}(input tersembunyi)${C_RESET} ")" answer
  echo ""
  answer="${answer:-$default}"
  PROMPT_RESULT="$answer"
}

prompt_confirm() {
  # prompt_confirm <label> [y/N] — hasil di PROMPT_RESULT
  local label="$1" default="${2:-N}" answer=""
  if [[ "$default" =~ ^[Yy]$ ]]; then
    read -r -p "$(echo -e "  ${C_CYAN}?${C_RESET} ${C_BOLD}${label}${C_RESET} ${C_DIM}[Y/n]${C_RESET} ")" answer
    answer="${answer:-$default}"
  else
    read -r -p "$(echo -e "  ${C_CYAN}?${C_RESET} ${C_BOLD}${label}${C_RESET} ${C_DIM}[y/N]${C_RESET} ")" answer
    answer="${answer:-$default}"
  fi
  PROMPT_RESULT="$answer"
}

# ------------------------------------------------------------------------------
#  System Checks & Utilities
# ------------------------------------------------------------------------------
check_root() {
  if [ "$EUID" -ne 0 ]; then
    echo ""
    log_error "Akses ditolak. Installer harus dijalankan sebagai root."
    echo -e "  ${C_DIM}Jalankan:${C_RESET} ${C_BOLD}sudo bash install.sh${C_RESET}"
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

  log_info "Environment  : ${C_BOLD}${OS} ${VER}${C_RESET}"
  log_info "Hostname     : ${C_BOLD}$(hostname)${C_RESET}"
  log_info "Waktu Server : ${C_BOLD}$(date '+%d %b %Y %H:%M:%S %Z')${C_RESET}"
}

generate_random_secret() {
  tr -dc 'a-zA-Z0-9' </dev/urandom | head -c 32
}
