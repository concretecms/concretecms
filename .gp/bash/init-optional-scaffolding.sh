#!/bin/bash
#
# SPDX-License-Identifier: MIT
# Copyright © 2021 Apolo Pena
#
# init-optional-scaffolding.sh
# Description:
# Installs frontend scaffolding, optional EXAMPLES and other packages as per the values set in starter.ini

# Load logger
. .gp/bash/workspace-init-logger.sh

# Load spinner
. .gp/bash/spinner.sh

# Workaround third party sass bug: https://github.com/apolopena/gitpod-laravel-starter/issues/140
hotfix140 () {
  local exit_code msg="Applying hotfix 140"
  log_silent "$msg..." && start_spinner "$msg"
  yes | npx add-dependencies sass@1.32.12 --dev 2> >(grep -v warning 1>&2) > /dev/null 2>&1
  exit_code=$?
  if [[ $exit_code == 0 ]]; then
    stop_spinner 0
    log_silent "SUCCESS: $msg"
  else
    stop_spinner 1
    log_silent -e "ERROR: $msg"
  fi
}

parse="bash .gp/bash/utils.sh parse_ini_value starter.ini"

install_react=$(eval "$parse" react install)
install_vue=$(eval "$parse" vue install)
install_bootstrap=$(eval "$parse" bootstrap install)
install_phpmyadmin=$(eval "$parse" phpmyadmin install)
react_auth=$(eval "$parse" react auth)
vue_auth=$(eval "$parse" vue auth)
bootstrap_auth=$(eval "$parse" bootstrap auth)
install_react_router_dom=$(eval "$parse" react-router-dom install)
rrd_ver=$(eval "$parse" react-router-dom version)
laravel_major_ver=$(bash .gp/bash/helpers.sh laravel_major_version)
laravel_ui_ver=$(bash .gp/bash/helpers.sh laravel_ui_version)
init_phpmyadmin=".gp/bash/init-phpmyadmin.sh"

# Any value set for EXAMPLE will build an example project thus superceding some directives in starter.ini
if [[ -n $EXAMPLE ]]; then
  # Hook: If there is no routes folder in version control then assume no scaffolding is in version control
  if ! git ls-files --error-unmatch routes > /dev/null 2>&1; then
    case $EXAMPLE in
      1)
        example_title="React Example with phpMyAdmin and extras - Questions and Answers"
        init_react_example=".gp/bash/examples/init-react-example.sh"
        install_react=1
        install_phpmyadmin=1
        install_react_router_dom=1
        rrd_ver='^5.2.0'
        ;;
      2)
        example_title="React Example without phpMyAdmin and no extras - Questions and Answers"
        init_react_example=".gp/bash/examples/init-react-example.sh"
        install_react=1
        install_phpmyadmin=0
        install_react_router_dom=1
        rrd_ver='^5.2.0'
        ;;
      3)
        example_title="React Typescript Example with phpMyAdmin - Questions and Answers"
        init_react_typescript_example=".gp/bash/examples/init-react-typescript-example.sh"
        install_react=1
        install_phpmyadmin=1
        install_react_router_dom=1
        rrd_ver='^5.2.0'
        ;;
      4)
        example_title="React Typescript Example without phpMyAdmin - Questions and Answers"
        init_react_typescript_example=".gp/bash/examples/init-react-typescript-example.sh"
        install_react=1
        install_phpmyadmin=0
        install_react_router_dom=1
        rrd_ver='^5.2.0'
        ;;
      10)
        example_title="Vue Example with phpMyAdmin and extras - Material Dashboard"
        init_vue_example=".gp/bash/examples/init-vue-example.sh"
        install_react=0
        install_vue=1
        vue_auth=1
        install_phpmyadmin=1
        ;;
      11)
        example_title="Vue Example without phpMyAdmin and no extras - Material Dashboard"
        init_vue_example=".gp/bash/examples/init-vue-example.sh"
        install_react=0
        install_vue=1
        vue_auth=1
        install_phpmyadmin=0
        ;;
      *)
        # Default example
        # Keep this block identical to case 1)
        example_title="React Example with phpMyAdmin and extras - Questions and Answers"
        init_react_example=".gp/bash/examples/init-react-example.sh"
        install_react=1
        install_phpmyadmin=1
        install_react_router_dom=1
        rrd_ver='^5.2.0'
        ;;
    esac
    log "EXAMPLE directive query parameter found"
    log "  --> Some directives in starter.ini will be superceded"
    log "Creating the example project"
    log "  --> $example_title"
  else
    log "WARNING: EXAMPLE$EXAMPLE requested but Laravel scaffolding seems to already be in version control"
    log "Skipping creation of the example project"
  fi # end check Laravel scaffolding is already in version control
fi # end check EXAMPLE query parameter

# Install phpMyAdmin if needed
if [[ $install_phpmyadmin == 1 ]];then
    # shellcheck source=.gp/bash/init-phpmyadmin.sh
    . "$init_phpmyadmin" 2>/dev/null || log_silent -e "ERROR: $(. $init_phpmyadmin 2>&1 1>/dev/null)"
fi

# BEGIN: Install Laravel ui if needed
if grep -q laravel/ui composer.json; then
  composer install
else
  has_frontend_scaffolding_install=$(bash .gp/bash/helpers.sh has_frontend_scaffolding_install)
  if [[ $has_frontend_scaffolding_install == 1 || -n $EXAMPLE ]]; then
    log "Optional installations that require laravel/ui scaffolding were found"
    # Assume we are using composer 2 or higher, check if the laravel/ui package has already been installed
    composer show | grep laravel/ui >/dev/null && __ui=1 || __ui=0
    if [[ $__ui == 1 ]]; then
      log "However it appears that laravel/ui has already been installed, skipping this installation."
    else
      log "Installing laravel/ui:$laravel_ui_ver scaffolding via Composer"
      composer require "laravel/ui:$laravel_ui_ver"
      err_code=$?
      if [ $err_code == 0 ]; then
        log "SUCCESS: laravel/ui scaffolding installed"
      else
        log -e "ERROR $err_code: There was a problem installing laravel/ui via Composer"
      fi # end check err_code 
    fi # end check laravel/ui already installed 
  fi # end check should install laravel ui 
fi # end check laravel/ui already in vcs but needs composer install
# Cleanup (since we use yarn)
[ -f package-lock.json ] && rm package-lock.json
# END: Install Laravel ui if needed

# BEGIN: Optional react, react-dom and react-router-dom installs
if [ $install_react == 1 ]; then
  version=$(eval "$parse" react version)
  __installed=$(bash .gp/bash/utils.sh node_package_exists react)
  [[ -z $version ]] && version_msg='' || version_msg=" version $version"
  [[ $react_auth != 1 ]] && auth_msg='' || auth_msg=' with --auth'
  log "React/React DOM install directive found in starter.ini"
  if [[ $__installed == 1 ]]; then
    log "However it appears that React/React DOM has already been installed, skipping this installation."
  else
    log "Installing React and React DOM$auth_msg"
    if [[ $react_auth == 1 ]]; then
      php artisan ui react --auth
    else
      php artisan ui react
    fi
    err_code=$?
    if [[ $err_code == 0 ]]; then
      log "SUCCESS: React and React DOM$auth_msg have been installed"
      if [ $install_react_router_dom == 1 ]; then
        if [[ -z "$rrd_ver" ]]; then
          sub_msg="Installing react-router-dom to the latest version"
          log "$sub_msg"
          yarn add react-router-dom --silent
        else
          sub_msg="Installing react-router-dom to semantic version $rrd_ver"
          log "$sub_msg"
          yarn add react-router-dom@$rrd_ver --silent
        fi
        err_code=$?
        if [[ $err_code == 0 ]]; then
          log "SUCCESS: $sub_msg"
        else
          log -e "ERROR: $sub_msg"
        fi
      fi
      log "  --> Installing node modules and running Laravel Mix"
      yarn install && npm run dev
      npm run dev
      if [[ -n $version ]]; then
        log "Upgrading react and react-dom to$version_msg"
        yarn upgrade "react@$version" "react-dom@$version"
      fi
      [[ $install_bootstrap == 1 ]] && log "Bootstrap install directive found but ignored. Already installed"
      [[ $install_vue == 1 ]] && log "Vue install directive found but ignored. The install of react superceded this"
    else
      log -e "ERROR $err_code: There was a problem installing React/React DOM$auth_msg"
    fi
  fi
fi
# END: Optional react, react-dom and react-router-dom installs
# BEGIN: Optional vue install
if [[ $install_vue == 1 && $install_react != 1 ]]; then
  version=$(eval "$parse" vue version)
  __installed=$(bash .gp/bash/utils.sh node_package_exists vue)
  [[ -z $version ]] && version_msg='' || version_msg=" version $version"
  [[ $vue_auth != 1 ]] && auth_msg='' || auth_msg=' with --auth'
  log "Vue install directive found in starter.ini"
  if [[ $__installed == 1 ]]; then
    log "However it appears that Vue has already been installed, skipping this installation."
  else
    log "Installing vue$auth_msg"
    if [[ $vue_auth == 1 ]]; then
      php artisan ui vue --auth
    else
      php artisan ui vue
    fi
    err_code=$?
    if [[ $err_code == 0 ]]; then
      log "SUCCESS: Vue$auth_msg has been installed"
      log "  --> Installing node modules and running Laravel Mix"
      yarn install && npm run dev
      npm run dev
      [[ $install_bootstrap == 1 ]] && log "Bootstrap install directive found but ignored. Already installed."
    else
      log -e "ERROR $err_code: There was a problem installing vue$auth_msg"
    fi
  fi
fi
# END: Optional vue install
# BEGIN: Optional bootstrap install
if [[ $install_bootstrap == 1 && $install_react != 1 && $install_vue != 1 ]]; then
  version=$(eval "$parse" bootstrap version)
  [[ -z "$version" ]] && version_msg='' || version_msg=" version $version"
  [[ $bootstrap_auth != 1 ]] && auth_msg='' || auth_msg=' with --auth'
  log "Bootstrap install directive found in starter.ini"
  log "Installing Bootstrap$auth_msg"
  if [[ $bootstrap_auth == 1 ]]; then
    php artisan ui bootstrap --auth
  elsegit 
    php artisan ui bootstrap
  fi
  err_code=$?
  if [[ $err_code == 0 ]]; then
    hotfix140
    log "SUCCESS: Bootstrap$version_msg$auth_msg has been installed"
    log "  --> Installing node modules and running Laravel Mix"
    yarn install && npm run dev
    npm run dev
    if [[ -n "$version" ]]; then
      log "Upgrading bootstrap to$version_msg"
      yarn upgrade "bootstrap@$version"
    fi
  else
    log -e "ERROR $err_code: There was a problem installing Bootstrap$auth_msg"
  fi
else
  version=$(eval "$parse" bootstrap version)
  [[ -z "$version" ]] && version_msg='' || version_msg=" version $version"
  if [[ -n $version && $install_bootstrap == 1 ]]; then
    log "Setting bootstrap to$version_msg"
    yarn upgrade "bootstrap@$version"
  fi
fi
# END: Optional bootstrap install
# END: optional frontend scaffolding installations

# BEGIN: optional example setup
# Initialize optional react example project
if [[ -n  $init_react_example ]];then
  [[ $laravel_major_ver -ne 8 ]] \
  && log -e "WARNING: React examples are only supported by Laravel version 8. Your Laravel version is $laravel_major_ver" \
  && log -e "WARNING: Ignoring the example requested: $example_title" \
  && exit
  # shellcheck source=.gp/bash/examples/init-react-example.sh
  . "$init_react_example" 2>/dev/null || log_silent -e "ERROR: $(. $init_react_example 2>&1 1>/dev/null)"
  exit
fi
# Initialize optional react typescript example project
if [[ -n  $init_react_typescript_example ]];then
  [[ $laravel_major_ver -ne 8 ]] \
  && log -e "WARNING: React examples are only supported by Laravel version 8. Your Laravel version is $laravel_major_ver" \
  && log -e "WARNING: Ignoring the example requested: $example_title" \
  && exit
  # shellcheck source=.gp/bash/examples/init-react-example.sh
  . "$init_react_typescript_example" 2>/dev/null || log_silent -e "ERROR: $(. $init_react_typescript_example 2>&1 1>/dev/null)"
  exit
fi
# Initialize optional vue example project
if [[ -n  $init_vue_example ]];then
  # shellcheck source=.gp/bash/examples/init-vue-example.sh
  . "$init_vue_example" 2>/dev/null || log_silent -e "ERROR: $(. $init_vue_example 2>&1 1>/dev/null)"
  exit
fi
# END: optional example setup