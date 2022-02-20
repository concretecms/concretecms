#!/bin/bash
#
# init-project.sh
# Description:
# Project specific initialization.

# Load logger
. .gp/bash/workspace-init-logger.sh

# BEGIN example code block - migrate database
# . .gp/bash/spinner.sh # COMMENT: Load spinner
# __migrate_msg="Migrating database"
# log_silent "$__migrate_msg" && start_spinner "$__migrate_msg"
# php artisan migrate
# err_code=$?
# if [ $err_code != 0 ]; then
#  stop_spinner $err_code
#  log -e "ERROR: Failed to migrate database"
# else
#  stop_spinner $err_code
#  log "SUCCESS: migrated database"
# fi
# END example code block - migrate database

