#!/bin/bash
#
# SPDX-License-Identifier: MIT
# Copyright © 2021 Apolo Pena
#
# workspace-init-logger.sh
# Description:
# Log wrapper for logging to workspace-init.log
#
# Usage:
# . workspace-init-logger.sh
# log "SUCCESS: Logging to workspace-init.log"
# log -e "ERROR: something went wrong"

# Log to the console and a file
log () {
  if [ -z "$2" ]; then
    bash "$GITPOD_REPO_ROOT/.gp/bash/utils.sh" log "$1" /var/log/workspace-init.log
  else
    bash "$GITPOD_REPO_ROOT/.gp/bash/utils.sh" log -e "$2" /var/log/workspace-init.log
  fi
}

# Log only to a file
log_silent () {
  if [ -z "$2" ]; then
    bash "$GITPOD_REPO_ROOT/.gp/bash/utils.sh" log_silent "$1" /var/log/workspace-init.log
  else
    bash "$GITPOD_REPO_ROOT/.gp/bash/utils.sh" log_silent -e "$2" /var/log/workspace-init.log
  fi
}