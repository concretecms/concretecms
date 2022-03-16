#!/bin/bash
#
# SPDX-License-Identifier: MIT
# Copyright © 2021 Apolo Pena
#
# helpers.sh
# Description:
# A variety of useful functions that depend on Gitpod and other
# binaries, aliases and functions such as code in .bashrc
#
# Notes:
# Do not execute this script without calling a function from it
# Additional Note: some functions use functions from .bashrc so the -i flag
# is the safest way to invoke functions from this script.
# Do not execute this script without calling a function from it.
#
# Usage: bash -i <function name> arg1 arg2 arg3 ...

gps_version() {
  local hard version title file
  hard="0.9"
  title="Concrete CMS Gitpod"
  file="$GITPOD_REPO_ROOT"/.gp/CHANGELOG.md
  if [[ -f $file ]]; then
    version=$(grep -oE "([[:digit:]]+\.[[:digit:]]+\.[[:digit:]]+)?" "$file" | head -n 1)
  fi
  if [[ -n  $version ]];then echo "$title $version"; else echo "$title $hard"; fi
}
# start_server
# Description:
# Starts up the default server or a specific server ($1)
#
# Usage:
# Example: start the default server
# start_server
start_server() {
  # Modified just for nginx.
  start_nginx
}

get_server_port() {
  case $(echo "$1" | tr '[:upper:]' '[:lower:]') in
    'php')
      echo 8000
      ;;
    'apache')
      echo 8001
      ;;
    'nginx')
      echo 8002
      ;;
    *)
      exit 127
      ;;
  esac
}

get_default_server_port() {
  get_server_port "nginx"
}

get_default_gp_url() {
  gp url "$(get_default_server_port)"
}

# Begin: persistance hacks
get_store_root() {
  echo "/workspace/$(basename "$GITPOD_REPO_ROOT")--store"
}

persist_file() {
  local err="helpers.sh: persist: error:"
  local store dest file
  store=$(get_store_root)
  dest="$store/$(dirname "${1#/}")"
  file="$dest/$(basename "$1")"
  mkdir -p "$store" && mkdir -p "$dest"
  [[ -f $1 ]] && cp "$1" "$file" || echo "$err $1 does not exist"
}

# For some reason $GITPOD_REPO_ROOT is not avaialable when this is called (from before task)
# So just pass it in from there as $1
restore_persistent_files() {
  local err="helpers.sh: restore_persistent_files: error:"
  # TODO make this dynamic
  local init_log_orig=/var/log/workspace-init.log
  local store
  store="/workspace/$(basename "$1")--store"
  local init_log="$store$init_log_orig"
  [[ -e $init_log ]] && cp "$init_log" $init_log_orig || echo "$err $init_log NOT FOUND"
}

inited_file () {
  echo "$(get_store_root)/is_inited.lock"
}

mark_as_inited() {
  local file store
  file=$(inited_file)
  store=$(get_store_root)
  mkdir -p "$(get_store_root)"
  [[ ! -e $file ]] && touch "$file"
}

is_inited() {
  [[ -e $(inited_file) ]] && echo 1 || echo 0
}
# End: persistance hacks

# php_fpm_conf
# Configures the php-fpm.conf depending on PHP version ($1) and the output file ($2)
# NOTE: If you want to configure this further parse the result of this from elsewhere.
php_fpm_conf() {
  [[ -z $1 || -z $2 ]] && 2>&1 echo "  ERROR: utils.sh --> php_fpm_conf(): Bad args. Script aborted" && exit 1
  echo "\
[global]
pid = /tmp/php$1-fpm.pid
error_log = /tmp/php$1-fpm.log

[www]
listen = 127.0.0.1:9000
listen.owner = gitpod
listen.group = gitpod

pm = dynamic
pm.max_children = 5
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3" > "$2"
}

# Call functions from this script gracefully
if declare -f "$1" > /dev/null
then
  # call arguments verbatim
  "$@"
else
  echo "helpers.sh: '$1' is not a known function name." >&2
  exit 1
fi