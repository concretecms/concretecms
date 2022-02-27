#!/bin/bash
#
# SPDX-License-Identifier: MIT
# Copyright © 2022 Apolo Pena
#
# init-gitpod.sh
# Description:
# Tasks to be run when a gitpod workspace is created for the first time.

# Load logger
. .gp/bash/workspace-init-logger.sh

# Load spinner
. .gp/bash/spinner.sh

# Log any potential mismatched configuration values such as the laravel version
. .gp/bash/init-check-config.sh

# Let the user know there will be a wait, then begin once MySql is initialized.
start_spinner "Initializing MySql..." &&
gp await-port 3306 &&
stop_spinner $?

# Globals
current_php_version="$(bash .gp/bash/utils.sh php_version)"

# BEGIN: Update npm if needed
target_npm_ver='^8'
min_target_npm_ver='8.3.2'
current_npm_ver=$(npm -v)
update_npm=$(bash .gp/bash/utils.sh comp_ver_lt "$current_npm_ver" "$min_target_npm_ver")
if [[ $update_npm == 1 ]]; then
  msg="Updating npm from $current_npm_ver to"
  log_silent "$msg $target_npm_ver" && start_spinner "$msg $target_npm_ver"
  npm install -g "npm@$target_npm_ver" &>/dev/null
  err_code=$?
  if [ $err_code != 0 ]; then
    stop_spinner $err_code
    log -e "ERROR $?: $msg a version >= $min_target_npm_ver"
  else
    stop_spinner $err_code
    log_silent "SUCCESS: $msg $(npm -v)"
  fi
fi
# END: Update npm if needed

# php fpm
# BEGIN: Autogenerate php-fpm.conf
php_fpm_conf_path=".gp/conf/php-fpm/php-fpm.conf"
active_php_version="$(. .gp/bash/utils.sh php_version)"
msg="Autogenerating $php_fpm_conf_path for PHP $active_php_version"
log_silent "$msg" && start_spinner "$msg"
if bash .gp/bash/helpers.sh php_fpm_conf "$active_php_version" "$php_fpm_conf_path"; then
  stop_spinner $?
  log_silent "SUCCESS: $msg"
else
  stop_spinner $?
  log -e "ERROR: $msg"
fi
# END: Autogenerate php-fpm.conf