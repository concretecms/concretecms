#!/bin/bash
#
# init-project.sh
# Description:
# Project specific initialization.

# Load logger
. .gp/bash/workspace-init-logger.sh

lvm=$(bash .gp/bash/helpers.sh laravel_major_version)

declare -a exit_codes=()
task_msg="Setting up Vue example: Material Dashboard"

log "$task_msg"

# Hook: If this file is not in VCS then assume all required scaffolding for this project is in VCS
if ! git ls-files --error-unmatch resources/views/dashboard.blade.php > /dev/null 2>&1; then
  if (( lvm > 6 )); then
    composer require laravel-frontend-presets/material-dashboard
  else
    composer require laravel-frontend-presets/material-dashboard v1.0.9
  fi
  exit_codes+=($?)
  php artisan ui material
  exit_codes+=($?)
else
  composer install
  exit_codes+=($?)
fi

composer dump-autoload
exit_codes+=($?)
php artisan migrate
exit_codes+=($?)
php artisan migrate --seed

if [[ $(echo "${exit_codes[@]}" | tr -d '[:space:]') =~ ^[0]+$ ]]; then
  log "SUCCESS: $task_msg"
else
  log -e "ERROR: $task_msg"
fi