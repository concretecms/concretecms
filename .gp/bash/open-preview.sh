#!/bin/bash
#
# SPDX-License-Identifier: MIT
# Copyright © 2022 Apolo Pena
#
# open-preview.sh
# Description:
# Conditionally opens an integrated preview browser
# either to the web root if no argument is passed in
# or to the specified path segment if $1 is passed in
# Can take a port option to open a specific port, for example op -3005
# In the case of using the port option, the path segment to open will be $2

function main() {
  local rp="$GITPOD_REPO_ROOT/.gp/"
  local path port
  [[ $1 == '-h' || $1 == '--help' ]] && cat "$rp/snippets/messages/op-help.txt" && exit 0
  if [[ $1 =~ ^-[1-9][0-9]+$ ]]; then
    port="${1:1}"
    path="/$2"
  else
    path="/$1"
    port=$(bash "$rp/bash/helpers.sh" get_default_server_port)
  fi
  if [[ $(bash "$rp/bash/helpers.sh" is_inited) == 0 ]]; then
    # shellcheck disable=SC1090,SC1091
    . "$rp/bash/spinner.sh" &&
    start_spinner "Opening preview when system is ready..."
    gp sync-await gitpod-inited &&
    stop_spinner 0 &&
    gp await-port "$port" &&
    gp preview "$(gp url "$port")$path" > /dev/null 2>&1
  else
    gp preview "$(gp url "$port")$path" > /dev/null 2>&1
  fi;
}

main "$@"