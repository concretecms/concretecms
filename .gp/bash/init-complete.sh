#!/bin/bash
#
# SPDX-License-Identifier: MIT
# Copyright © 2021 Apolo Pena
#
# init-complete.sh
# Description:
# Code to be run just once at the very end of workspace initialization.
# 
# Notes:
# Always call this file last from the 'init' command in .gitpod.yml

# Load logger
. .gp/bash/workspace-init-logger.sh

# Inject routes/web.php
allow_mixed_web=$(bash .gp/bash/utils.sh parse_ini_value starter.ini laravel allow_mixed_web)
[[ $allow_mixed_web != 0 ]] && bash .gp/bash/directives/allow-mixed-web.sh

# Add Workspace/Project composer bin folder to $PATH
export PATH="$PATH:$HOME/.config/composer/vendor/bin:$GITPOD_REPO_ROOT/vendor/bin"

# Cleanup
if rm -rf /home/gitpod/laravel-starter;then
  log "CLEANUP SUCCESS: removed ~/laravel-starter"
fi

# Summarize results
bash .gp/bash/helpers.sh show_first_run_summary

# Persist the workspace-init.log since the .gitpod.Dockerfile will wipe it out and it wont come back after the first run
bash .gp/bash/helpers.sh persist_file /var/log/workspace-init.log

# Set initialized flag - Keep this at the bottom of the file
bash .gp/bash/helpers.sh mark_as_inited
gp sync-done gitpod-inited
