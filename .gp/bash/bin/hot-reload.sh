#!/bin/bash
#
# SPDX-License-Identifier: MIT
# Copyright © 2021 Apolo Pena
#
# hot-reload.sh
# Description:
# cli for hot reloading via browser-sync and Laravel Mix
# Public API commands: setup, start, stop, and refresh

# shellcheck source=.gp/bash/spinner.sh
. "$GITPOD_REPO_ROOT"/.gp/bash/spinner.sh

c_red='\e[38;5;124m'
c_orange='\e[38;5;208m'
c_green='\e[38;5;76m'
c_blue='\e[38;5;147m'
c_hot_pink="\e[38;5;213m"
c_end='\e[0m'

injection_file="$GITPOD_REPO_ROOT"/webpack.mix.js
snippet=.gp/snippets/laravel/webpack/browser-sync.snippet

_version() {
  echo -e "hot-reload cli (MIT license) v1.0.0\nCopyright (C) 2021 Apolo Pena "
}

_pass_msg() {
  echo -e "$c_green""SUCCESS$c_end:$c_end $1"
}

_fail_msg() {
  local msg script_path prefix
  if [[ $1 == '--show-path' ]]; then
    msg="$2"
    prefix="ERROR "
    script_path=$(readlink -f "$0")
  else
    msg="$1"
    prefix="ERROR"
  fi
  echo -e "$c_red$prefix$c_end$c_blue$script_path$c_end$c_red:$c_end$c_orange $msg$c_end"
}

_bad_cmd_msg() {
  local raw_cmd cmd
  [[ -z $1 ]] && return 1
  if [[ $1 == '-S' ]]; then raw_cmd="$2"; else raw_cmd="$1"; fi
  cmd="'$(echo "$raw_cmd" | awk '{print $1;}')'"
  _fail_msg "Unknown command $cmd"
  [[ $1 == '-S' ]] && echo "Run hot-reload --help for usage"
}

_help() {
  [[ -f  $1 ]] && cat "$1" && return 0
  _fail_msg "help data file did not exist at $1"
  return 1
}

_show_help() {
  local data_path="$GITPOD_REPO_ROOT/.gp/snippets/messages/hot-reload"
  local data_file="$data_path/$1-help.txt"
  case $1 in
    setup)
      ;&
    start)
      ;&
    stop)
      ;&
    refresh)
      ;&
    main)
      _help "$data_file"
      ;;
    *)
      [[ $1 =~ (^[ ]*$) ]] && _help "$data_path/main-help.txt"
      _bad_cmd_msg "$1"
      ;;
  esac
}

# BEGIN: Public API
setup() {
  [[ -n $1 ]] &&  _handle_cmd_args "$1" setup
  local snippet_file="$GITPOD_REPO_ROOT/$snippet"
  local main_msg="$c_hot_pink""Setting up hot reload system$c_end"
  echo -e "$main_msg"
  # Install browser-sync packages
  if [[ ! -f $GITPOD_REPO_ROOT/node_modules/browser-sync/LICENSE ]]; then
    msg="Installing browser-sync packages..."
    start_spinner "$msg"
    yarn add browser-sync browser-sync-webpack-plugin  --silent 2> >(grep -v warning 1>&2) > /dev/null 2>&1
    exit_code=$?
    if [[ $exit_code == 0 ]]; then
      stop_spinner $exit_code "\b \n$(_pass_msg "$msg")"
    else
      stop_spinner $exit_code "\b \n$(_fail_msg "$msg")"
      exit 1
    fi
  else
    _pass_msg "Browser-sync packages have already been installed"
  fi
  # Inject webpack.mix.js
  msg="Injecting $injection_file file"
  if [[ -e $injection_file ]]; then
    [[ ! -e $snippet_file ]] && fail=1 && e_msg="Missing injection file $snippet_file"
    if ! grep -q "Injected from $snippet" "$injection_file"; then
      start_spinner "$msg..." && sleep .5
      cat "$snippet" >> "$injection_file" 2> /dev/null
      exit_code=$?
      if [[ $exit_code -ne 0  || $fail -eq 1 ]]; then 
        [[ $fail -eq 1 ]] && msg=$e_msg
        stop_spinner 1 "\b \n$(_fail_msg "$msg")"
        exit 1
      else
        stop_spinner 0 "\b \n$(_pass_msg "$msg")"
      fi # end check success or failure
    else
      _pass_msg "$injection_file has already been injected"
    fi # end grep check if file is already injected
  else
    start_spinner "$msg"
    sleep .5
    stop_spinner 1 "\b \n$(_fail_msg "no $injection_file file to inject")"
    exit 1
  fi
  # Run Laravel Mix, to initialize browser-sync-webpack-plugin
  msg="Running Laravel Mix..."
  start_spinner "$msg"
  if yarn run mix > /dev/null 2>&1; then
    stop_spinner 0 "\b \n$(_pass_msg "$msg")"
  else
    stop_spinner 1 "\b \n$(_fail_msg "$msg")"
    exit 1
  fi
  _pass_msg "$main_msg"
}

start() {
  [[ -n $1 ]] &&  _handle_cmd_args "$1" start
  [[ $1 == '-h' || $1 == '--help' ]] && _show_help start && exit
  if ! _check_setup start; then exit 1; fi
  if pgrep -f "^node.*yarn run watch"; then
    echo "Hot reload already in progress at $(gp "$(gp url 3005)")"
    exit 5
  fi
  yarn run watch
}

stop() {
  [[ -n $1 ]] && _handle_cmd_args "$1" stop
  [[ $1 == '-h' || $1 == '--help' ]] && _show_help stop && exit
  if ! _check_setup stop; then exit 1; fi
  local pid _tty
  pid=$(pgrep -f "^node.*yarn run watch")
  if [[ -n $pid ]]; then
    _tty=$(ps "$pid" | sed -n '2 p' | grep -Po "pts\/[0-9]") &&
    pkill -2 -t "$_tty" &&
    _pass_msg "Hot reload server has been stopped" &&
    exit
  fi
  _fail_msg "No Laravel Mix watch process detected"
  _fail_msg "There is no hot reload server to stop"
}

refresh() {
  [[ -n $1 ]] && _handle_cmd_args "$1" refresh
  local pid
  pid=$(pgrep -f "^node.*yarn run watch")
  if [[ -n $pid ]]; then
    gp preview "$(gp url 3005)"?bust=cache && gp preview "$(gp url 3005)"
    local exit_code=$?
    [[ $exit_code -eq 0 ]] && _pass_msg "hot reload browser was refreshed" && exit
    _fail_msg "gp command failed, could not refresh hot reload browser" && exit
  fi
  _fail_msg "Hot reload server is not running, nothing to refresh"
}
# END: Public API

_handle_cmd_args() {
  [[ -z $2 ]] && _fail_msg "Internal, _handle_cmd_args: missing second parameter" && exit 1
  [[ $2 != 'setup' && $2 != 'start' && $2 != 'stop' && $2 != 'refresh' ]] && \
  _fail_msg --show-path "Internal, _handle_cmd_args: illegal cmd $2" && exit 1
  if [[ $1 =~ ^\- ]]; then
    if ! _validate_flag "$1"; then 
      exit 1;
    else
      [[ $1 == '-h' || $1 == '--help' ]] && \
      _show_help "$2" && exit || _fail_msg "$2: Unsupported flag $1" && exit 1
    fi
  fi
  local all_spaces="^[ ]*$"
  [[ -z $1 || $1 =~ $all_spaces ]] || _fail_msg "$2: Illegal argument: $1"; exit 1
}

_is_setup() {
  [[ ! -f "$GITPOD_REPO_ROOT/node_modules/browser-sync/LICENSE" ]] && echo 0 && exit
  if ! grep -q "Injected from $snippet" "$injection_file"; then
    echo 0 && exit
  fi
  echo 1
}

_check_setup() {
  local verb aok
  aok=$(_is_setup)
  [[ $1 == 'start' ]] && verb='start'; [[ $1 == 'stop' ]] && verb='stop'
  local err_msg="Cannot $verb the hot reload server because it has not been setup. Run:$c_end hot-reload setup"
  if ((aok));then return 0; else _fail_msg "$err_msg"; return 1; fi
}

_show_deps() {
  local file="$GITPOD_REPO_ROOT"/.gp/snippets/messages/hot-reload/deps.txt
  [[ ! -f $file ]] && _fail_msg --show-path "Internal: missing data file $file" && exit 1
  cat "$file"
}

# Rudimentary flag validation
_validate_flag() {
  # All dashes, goodbye
  [[ $1 =~ ^[\-]*$ ]] && _fail_msg "Bad flag syntax: $1" && return 1
  # Starts with any number of spaces or no spaces and then more than two dashes in a row, goodbye
  [[ $1 =~ (^[ ]*[\-]{3,}) ]] && _fail_msg "Bad flag syntax: $1" && return 1
  return 0
}

# If it looks like a flag, handle it as such
if [[ $1 =~ ^\- ]]; then
  if ! _validate_flag "$1"; then exit 1; fi
  if [[ $1 == '-v' || $1 == '--version' ]]; then
    _version
  elif [[ $1 == '-h' || $1 == '--help' ]]; then
    _show_help
  elif [[ $1 == '-s' || $1 == '--show-deps' ]]; then
    _show_deps
  else
    _fail_msg "Unsupported flag $1" && exit 1
  fi
  exit 0
fi



# Call functions from this script gracefully
if declare -f "$1" > /dev/null
then
  # call arguments verbatim
  "$@"
else
  if [[ -z $1 ]]; then _show_help; else _bad_cmd_msg -S "$1" >&2; fi
  exit 1
fi
