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

# BEGIN: Install https://www.ioncube.com/loaders.php
if [[ $(bash .gp/bash/utils.sh parse_ini_value starter.ini ioncube install) == 1 ]]; then
  if [[ $current_php_version == 7.4 ]]; then
    msg="Installing ioncube loader"
    log_silent "$msg" && start_spinner "$msg" \
    && wget http://downloads3.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz -O /tmp/ioncube.tar.gz \
    && tar xzf /tmp/ioncube.tar.gz -C /tmp \
    && sudo cp /tmp/ioncube/ioncube_loader_lin_7.4.so /usr/lib/php/20190902/ioncube_loader_lin_7.4.so \
    && sudo bash -c 'echo "zend_extension=ioncube_loader_lin_7.4.so" > /etc/php/7.4/apache2/conf.d/10-ioncube.ini' \
    && sudo bash -c 'echo "zend_extension=ioncube_loader_lin_7.4.so" > /etc/php/7.4/cli/conf.d/10-ioncube.ini' \
    && rm -rf /tmp/ioncube.tar.gz /tmp/ioncube
    err_code=$?
    if [[ $err_code != 0 ]]; then
      stop_spinner $err_code
      log -e "ERROR: $msg"
    else
      stop_spinner $err_code
      log "SUCCESS: $msg"
    fi
  else
    log "WARNING: ioncube loader cannot be installed with PHP $current_php_version. Fix your starter.ini"
  fi
fi
# END: Install https://www.ioncube.com/loaders.php

# BEGIN: Bootstrap non-laravel
if [[ ! -d $GITPOD_REPO_ROOT/routes ]]; then
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

  # BEGIN: parse .vscode/settings.json
  if [[ $(bash .gp/bash/utils.sh parse_ini_value starter.ini development vscode_disable_preview_tab) == 1 ]]; then
    msg="parsing .vscode/settings.json as per starter.ini"
    log_silent "$msg" && start_spinner "$msg"
    if bash .gp/bash/utils.sh add_file_to_file_after '{' ".gp/conf/vscode/disable_preview_tab.txt" ".vscode/settings.json"; then
      stop_spinner $?
      log_silent "SUCCESS: $msg"
    else
      stop_spinner $?
      log -e "ERROR: $msg"
    fi
  fi
  # END: parse .vscode/settings.json
fi;
# END: Bootstrap non-laravel

# BEGIN: Bootstrap Laravel
if [ ! -d "$GITPOD_REPO_ROOT/vendor" ]; then

  # Handle laravel README.md
  if [[ $(bash .gp/bash/utils.sh parse_ini_value starter.ini laravel include_readme) == 1 ]]; then
    if [[ ! -f "$GITPOD_REPO_ROOT/README_LARAVEL.md" ]]; then 
      mv /home/gitpod/laravel-starter/README.md "$GITPOD_REPO_ROOT/README_LARAVEL.md"
    else
    rm /home/gitpod/laravel-starter/README.md
    fi
  else
    rm /home/gitpod/laravel-starter/README.md
  fi
  
  # BEGIN: rsync any new Laravel project files from the docker image to the repository
  msg="rsync $(php ~/laravel-starter/artisan --version) from ~/laravel-starter to $GITPOD_REPO_ROOT"
  log_silent "$msg" && start_spinner "$msg"
  shopt -s dotglob
  grc -c .gp/conf/grc/rsync-stats.conf \
  rsync -rlptgoD --ignore-existing --stats --human-readable /home/gitpod/laravel-starter/ "$GITPOD_REPO_ROOT"
  err_code=$?
  if [ $err_code != 0 ]; then
    stop_spinner $err_code
    log -e "ERROR: $msg"
  else
    stop_spinner $err_code
    log_silent "SUCCESS: $msg"
  fi
  # END: rsync any new Laravel project files from the docker image to the repository

  # BEGIN: Autogenerate phpinfo.php
  if [[ $(bash .gp/bash/utils.sh parse_ini_value starter.ini PHP generate_phpinfo) == 1 ]]; then
    if [[ -z $GITPOD_REPO_ROOT ]]; then 
      p="public/phpinfo.php"; 
    else
      p="$GITPOD_REPO_ROOT/public/phpinfo.php"
    fi
    msg="generating phpinfo.php file in /public "
    log_silent "$msg" && start_spinner "$msg"
    if echo "<?php phpinfo( ); ?>" > "$p"; then
      stop_spinner $?
      log_silent "SUCCESS: $msg"
    else
      stop_spinner $?
      log -e "ERROR: $msg"
    fi
  fi
  # END: Autogenerate phpinfo.php

  # Move, rename or merge any project files that need it
  [[ -f "LICENSE" && -d ".gp" && ! -f .gp/LICENSE ]] && mv -f LICENSE .gp/LICENSE
  [[ -f "README.md" && -d ".gp" && ! -f .gp/README.md ]] && mv -f README.md .gp/README.md
  [[ -f "CHANGELOG.md" && -d ".gp" && ! -f .gp/CHANGELOG.md ]] && mv -f CHANGELOG.md .gp/CHANGELOG.md

  # Remove potentially cached phpmyadmin installation if phpmyadmin should not be installed
  if [ "$(bash .gp/bash/utils.sh parse_ini_value starter.ini phpmyadmin install)" == 0 ]; then
    [ -d "public/phpmyadmin" ] && rm -rf public/phpmyadmin
  fi

  # BEGIN: Configure .editorconfig
  if [ -e .editorconfig ]; then
    ec_type=$(bash .gp/bash/utils.sh parse_ini_value starter.ini .editorconfig type)
    case $(echo "$ec_type" | tr '[:upper:]' '[:lower:]') in
      'laravel-js-2space')
        cp .gp/conf/editorconfig/laravel-js-2space .editorconfig
      ;;
      'none')
        rm .editorconfig
      ;;
      *)
        #Ignore invalid types
      ;;
    esac
  fi
  # END: Configure .editorconfig
  # BEGIN Laravel .env injection
  if [ -e .env ]; then
    msg="Injecting Laravel .env file with APP_URL and ASSET_URL"
    log_silent "$msg" && start_spinner "$msg"
    url=$(gp url "$(bash .gp/bash/helpers.sh get_default_server_port)")
    err_code=$?
    if [[ $url =~ ^https?:// ]];then
      sed -i'' "s#^APP_URL=http://localhost*#APP_URL=$url\nASSET_URL=$url#g" .env
      err_code=$?
    else
      url="$(echo -e "$url" | head -1)"
      log_silent -e "ERROR: malformed url: $url"
    fi
    if [[ $err_code != 0 ]]; then
      stop_spinner 1
      log -e "ERROR: Could not inject Larvel .env file with the url: $url"
    else
      stop_spinner 0
      log_silent "SUCCESS: Laravel .env APP_URL and ASSET_URL was set to $url"
      log_silent "  If you want to serve the project on a different port then"
      log_silent "  change the port number in APP_URL and ASSET_URL in .env"
    fi
  else
    log 'ERROR: no Laravel .env file to inject'
  fi
  # END: Laravel .env injection

  # Create laravel database if it does not exist
  __laravel_db_exists=$(mysqlshow  2>/dev/null | grep laravel >/dev/null 2>&1 && echo "1" || echo "0")
  if [[ $__laravel_db_exists == 0 ]]; then
    msg="Creating database: laravel"
    log_silent "$msg" && start_spinner "$msg"
    mysql -e "CREATE DATABASE laravel;"
    err_code=$?
    if [ $err_code != 0 ]; then
      stop_spinner $err_code
      log -e "ERROR: $msg"
    else
      stop_spinner $err_code
      log_silent "SUCCESS: $msg"
    fi
  fi
  
  # Install https://github.com/github-changelog-generator/github-changelog-generator
  installed_changelog_gen=$(bash .gp/bash/utils.sh parse_ini_value starter.ini github-changelog-generator install)
  if [ "$installed_changelog_gen" == 1 ]; then
    msg="Installing github-changelog-generator"
    log_silent "$msg" && start_spinner "$msg" &&
    # Hotfix https://github.com/github-changelog-generator/github-changelog-generator/issues/1003
    # See https://github.com/apolopena/gitpod-laravel-starter/issues/190
    gem install async -v '~> 1.29' &&
    gem install github_changelog_generator --no-document --silent
    err_code=$?
    if [[ $err_code != 0 ]]; then
      stop_spinner $err_code
      log -e "ERROR: $msg"
    else
      stop_spinner $err_code
      log "SUCCESS: $msg"
    fi
  fi

  # Install node packages and run laravel mix blindly here since at this stage there is no viable
  # hook for when laravel/ui frontend scaffolding (react, vue or bootstrap) is in version control but the
  # workspace is initializing for the first time. This is the only way we can establish a hook
  # for init-optional-scaffolding.sh to determine if it should bypass the php artisan ui command
  # since the hook that init-optional-scaffolding.sh uses is to look for a directory in node_modules
  # named react, vue or bootstrap. Without this hook, project code such as app.js gets overwitten.
  if [[ -f "package.json"  && ! -d "node_modules" ]]; then
    msg="Installing node modules"
    log "$msg"
    yarn install
    err_code=$?
    if [ $err_code != 0 ]; then
      log -e "ERROR $?: $msg"
    else
      log "SUCCESS: $msg"
    fi
    log " --> Running Laravel Mix"
    npm run dev
    log " --> Running of Laravel Mix complete"
  fi # end node_modules/ check
fi # end vendor/ check for bootstrapping
# END: Bootstrap Laravel
