#!/bin/bash
#
# SPDX-License-Identifier: MIT
# Copyright © 2021 Apolo Pena
#
# init-gitpod.sh
# Description:
# Bootstraps an existing phpmyadmin installation.

# Load logger
. .gp/bash/workspace-init-logger.sh

# Load spinner
. .gp/bash/spinner.sh

# regexp pattern for checking an array of exit codes
all_zeros_reg='^[0]+$'

# BEGIN: Boostrap phpMyAdmin
# If the docker image has cached the directive to not install phpmyadmin, install it now.
if [[ ! -d "public/phpmyadmin" ]]; then
  msg="Installing phpmyadmin"
  log_silent "$msg" && start_spinner "$msg"
  cd public &&  composer create-project phpmyadmin/phpmyadmin
  err_code=$?
  if [ $err_code != 0 ]; then
    stop_spinner 1
    log -e "ERROR: $msg"
    exit 1
  else
    cd ..
    stop_spinner 0
    log "SUCCESS: $msg"
  fi
fi

# BEGIN: Parse configuration file
if [ -e public/phpmyadmin/config.sample.inc.php ]; then
  msg="Creating file public/phpmyadmin/config.inc.php"
  log_silent "$msg" && start_spinner "$msg"
  cp public/phpmyadmin/config.sample.inc.php public/phpmyadmin/config.inc.php
  err_code=$?
  if [ $err_code != 0 ]; then
    stop_spinner $err_code
    log -e "ERROR: Failed $msg"
  else
    stop_spinner $err_code
    log_silent "SUCCESS: $msg"
  fi

  # Inject additional configuration into public/phpmyadmin/config.inc.php at line 69
  sed "69r .gp/snippets/phpmyadmin/conf.snippet" < public/phpmyadmin/config.inc.php > __tmp
  mv __tmp public/phpmyadmin/config.inc.php 

  # Setup Blowfish secret
  msg="Parsing blowfish secrect in public/phpmyadmin/config.inc.php"
  log_silent "$msg" && start_spinner "$msg"
  __bfs=$(bash .gp/bash/utils.sh generate_string 32)
  # shellcheck disable=2154,1087
  sed -i'' "s#\\$cfg['blowfish_secret'] = '';#\\$cfg['blowfish_secret'] = '$__bfs';#g" public/phpmyadmin/config.inc.php
  err_code=$?
  if [ $err_code != 0 ]; then
    stop_spinner $err_code
    log -e "ERROR: Failed $msg"
  else
    stop_spinner $err_code
    log_silent "SUCCESS: $msg"
  fi

  # Setup storage configuration
  msg="Uncommenting storage configuration in public/phpmyadmin/config.inc.php"
  log_silent "$msg" && start_spinner "$msg"
  sed -i "/'controluser'/,/End of servers configuration/ s/^\/\/ *//" public/phpmyadmin/config.inc.php
  err_code=$?
  if [ $err_code != 0 ]; then
    stop_spinner $err_code
    log -e "ERROR: Failed $msg"
  else
    stop_spinner $err_code
    log_silent "SUCCESS: $msg"
  fi
fi
# END: Parse configuration file

# Setup phpmyadmin db and storage tables
msg='Configuring phpmyadmin db and storage tables'
log_silent "$msg" && start_spinner "$msg"
mysql < public/phpmyadmin/sql/create_tables.sql
if [ $err_code != 0 ]; then
  stop_spinner $err_code
  log -e "ERROR: $msg"
else
  stop_spinner $err_code
  log_silent "SUCCESS: $msg"
fi

# Create super user account for phpmyadmin
msg="Creating phpmyadmin superuser: pmasu"
log_silent "$msg" && start_spinner "$msg"
mysql -e "CREATE USER 'pmasu'@'%' IDENTIFIED BY '123456';"
mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'pmasu'@'%';"
err_code=$?
if [ $err_code != 0 ]; then
  stop_spinner $err_code
  log -e "ERROR: failed to create phpmyadmin superuser: pmasu"
else
  log_silent "SUCCESS: $msg"
  stop_spinner $err_code
fi

# Create control user account for phpmyadmin (used for advanced/storage features)
msg="Creating phpmyadmin control user"
error_codes=()
log_silent "$msg" && start_spinner "$msg"
mysql -e "CREATE USER 'pma'@'localhost' IDENTIFIED BY 'pmapass';"
error_codes+=($?)
mysql -e "GRANT ALL PRIVILEGES ON \`phpmyadmin\`.* TO 'pma'@'localhost' WITH GRANT OPTION;"
error_codes+=($?)
mysql -e "FLUSH PRIVILEGES;"
error_codes+=($?)
error_codes_flat=$(echo "${error_codes[*]}" | tr -d '[:space:]')
if [[ $error_codes_flat =~ $all_zeros_reg ]]; then
  stop_spinner 0
  log_silent "SUCCESS: $msg"
else
  stop_spinner 1
  log -e "ERROR ${error_codes[*]}: $msg"
fi

# Install node modules
if [ ! -d 'public/phpmyadmin/node_modules' ]; then
  msg="Installing phpmyadmin node modules"
  log "$msg"
  if cd public/phpmyadmin && yarn install && cd ../../; then
    log_silent "SUCCESS: $msg"
    log_silent "To login to phpmyadmin:"
    log_silent "  --> 1. Make sure you are serving it with apache"
    log_silent "  --> 2. In the browser go to $(bash .gp/bash/helpers.sh get_default_gp_url)/phpmyadmin/"
    log_silent "  --> 3. You should be able to login here using the defaults. user: pmasu, pw: 123456"
    log_silent "Make sure you change the default passwords for the phpmyadmin accounts."
    log_silent "For help with updating phpmyadmin passwords, run the alias help-update-pma-pws"
  else
    log -e "ERROR: $msg. Try installing them manually."
  fi
fi
# END: Boostrap phpMyAdmin