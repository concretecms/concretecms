#!/bin/bash
#
# SPDX-License-Identifier: MIT
# Copyright © 2021 Apolo Pena
#
# init-vue-example.sh
# Description:
# Initial setup for the gitpod-laravel-starter Vue example.

# Load logger
. .gp/bash/workspace-init-logger.sh

main () {
  local src="$GITPOD_REPO_ROOT"/.gp/snippets/projects/material-dashboard-example.sh
  local dest="$GITPOD_REPO_ROOT"/.gp/bash/init-project.sh
  if [[ -f $src ]]; then 
    cp -f "$src" "$dest"
  else
    log -e "ERROR: Preparing EXAMPLE $EXAMPLE, missing $src"
  fi
}
  
main