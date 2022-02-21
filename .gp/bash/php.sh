#!/bin/bash
# shellcheck disable=SC1091
#
# SPDX-License-Identifier: MIT
# Copyright © 2022 Apolo Pena
#
# php.sh
# Description:
# Installs an optional PHP version if specified in the [PHP] section of starter.ini
# If an optional PHP version is installed then the original PHP version will be uninstalled and purged
#
# Notes:
# This script assumes it is being run from .gitpod.Dockerfile as a sudo user
# and that all of this scripts dependencies have already been copied to /tmp
# If you change this script you must force a rebuild of the docker image
#
# Gitpod currently implements PHP as an embedded Apache module (prefork MPM)
# This script assumes that case by installing libapache2-mod-phpX.X when an optional
# php version is required when X.X is the supported PHP version specified in starter.ini

log='/var/log/workspace-image.log'
php7_4='php7.4 php7.4-fpm php7.4-dev libapache2-mod-php7.4 php7.4-bcmath php7.4-ctype php7.4-curl php-date php7.4-gd php7.4-intl php7.4-json php7.4-mbstring php7.4-mysql php-net-ftp php7.4-pgsql php7.4-sqlite3 php7.4-tokenizer php7.4-xml php7.4-zip'
latest_php="$(. /tmp/utils.sh php_version)"
php_version=
gp_php_url='https://github.com/gitpod-io/workspace-images/blob/master/full/Dockerfile'

purge_gp_php() {
  local msg="Purging existing PHP $latest_php installation"
  echo "  $msg" | tee -a $log
  sudo apt-get purge -y "php${latest_php%%.*}.*"
  local ec=$?
  if [[ $ec -eq 0 ]]; then
    echo "    SUCCESS: $msg" | tee -a $log
  else
    2>&1 echo "    ERROR: $msg" | tee -a $log
    return 1
  fi

  msg="Cleaning up from the purge of PHP $latest_php"
  echo "  $msg" | tee -a $log
  sudo apt-get autoclean &&
  sudo apt-get autoremove
  ec=$?
  if [[ $ec -eq 0 ]]; then
    echo "    SUCCESS: $msg" | tee -a $log
  else
    2>&1 echo "    ERROR: $msg" | tee -a $log
  fi
}

install_php() {
  local msg=
  local ppa=
  ppa=$(. /tmp/utils.sh parse_ini_value /tmp/starter.ini PHP ppa)
  # Strip any potential leading or tailing whitepsace from the parsed ppa value
  ppa=$(bash /tmp/utils.sh trim_external "$ppa")

  # Uncomment to debugging installed packages in the build image step
  # sudo a2query -m

  # Disable existing php mod and prefork, this will automatically be reinstated when PHP is installed
  sudo a2dismod "php$latest_php" mpm_prefork

  # Conditionally remove ppa:ondrej/php (if directed to do so AND it exists)
  if [[ $ppa != "ondrej" ]]; then
    if grep ^deb /etc/apt/sources.list /etc/apt/sources.list.d/* | grep -wq "ondrej/php"; then
      msg="Removing ppa:ondrej/php (as specified in starter.ini)"
      echo "  $msg" | tee -a $log
      if sudo add-apt-repository -y --remove "ppa:ondrej/php"; then
        echo "    SUCCESS: $msg" | tee -a $log
        echo "      The standard OS ppa will be used to install PHP $php_version"
      else
        2>&1 echo "    ERROR: $msg" | tee -a $log
      fi # end removal of ppa:ondrej/php
    fi # end check if ppa:ondrej/php is active
  fi # end check ppa directive in starter.ini


  msg="Installing PHP $php_version as specified in starter.ini"
  echo "  $msg" | tee -a $log
  echo 'debconf debconf/frontend select Noninteractive' | sudo debconf-set-selections \
    && sudo apt-get update -q \
    && sudo apt-get -yqo Dpkg::Options::="--force-confnew" install "${all_packages[@]}"
  local ec=$?
  if [[ $ec -eq 0 ]]; then
    echo "    SUCCESS: $msg" | tee -a $log
    echo "      The following packages were installed: ${all_packages[*]}"
  else
    2>&1 echo "    ERROR: $msg" | tee -a $log
    2>&1 echo "      One or more of the following packages failed to install: ${all_packages[*]}" | tee -a $log
    return 1
  fi
}

configure_php() {
  local msg="Setting PHP config, phar and phpize from $latest_php to $php_version"
  echo "  $msg" | tee -a $log
  sudo update-alternatives --set php "/usr/bin/php$php_version" &&
  sudo update-alternatives --set phpize "/usr/bin/phpize$php_version" &&
  sudo update-alternatives --set phar "/usr/bin/phar$php_version" &&
  sudo update-alternatives --set phar.phar "/usr/bin/phar.phar$php_version" &&
  sudo update-alternatives --set php-config "/usr/bin/php-config$php_version"
  local ec=$?
  if [[ $ec -eq 0 ]]; then
    echo "    SUCCESS: $msg" | tee -a $log
  else
    2>&1 echo "    ERROR: $msg" | tee -a $log
    return 1
  fi
}

configure_apache() {
  local loc=/var/log/apache2
  mv /tmp/apache2.conf /etc/apache2/apache2.conf
  sudo chown gitpod:gitpod "$loc/access.log" "$loc/error.log" "$loc/other_vhosts_access.log"
  return $?
}

keep_existing_php() {
  local msg1 msg2=

  [[ $1 == 'fallback' ]] &&
    msg1="  WARNING: unsupported PHP version $php_version found in starter.ini." &&
    msg2="Falling back to the existing PHP version $latest_php as specified in $gp_php_url" &&
    echo "$msg1 $msg2" | tee -a $log &&
    echo "END: php.sh" | tee -a $log  &&
    return 0

  msg1="  Using the existing 'gitpodlatest' version of PHP ($latest_php) as specified in $gp_php_url" &&
  echo "$msg1" | tee -a $log &&
  echo "END: php.sh" | tee -a $log
}

# BEGIN: MAIN
echo "BEGIN: php.sh" | tee -a $log
php_version=$(. /tmp/utils.sh parse_ini_value /tmp/starter.ini PHP version)
ec=$?
if [[ $ec -ne 0 ]]; then
  2>&1 echo "  WARNING: could not parse /tmp/starter.ini. Defaulting PHP version to 'gitpodlatest' as specified in $gp_php_url" | tee -a $log
  php_version='gitpodlatest'
fi

if [[ $php_version == '7.4' ]]; then
  IFS=" " read -r -a all_packages <<< "$php7_4"
elif [[ $php_version == 'gitpodlatest' ]]; then
  keep_existing_php
  exit 0
else
  keep_existing_php 'fallback'
  exit 0
fi

# Rebuild the package list so we can find the Gitpod installed PHP in order to purge it
sudo apt-get update

# Installing multiple versions of PHP is possible but adds alot of complexity and decreases performance
# so remove the version of php that was installed via the gitpod base image before we get started
if ! purge_gp_php; then
  2>&1 echo "  php.sh was aborted: Existing php installation failed to be purged!" | tee -a $log && exit 1
fi

# Install PHP
if ! install_php; then
  2>&1 echo "  php.sh was aborted: Optional php installation failed!" | tee -a $log && exit 1
fi

# Configure PHP
if ! configure_php; then
  2>&1 echo "  php.sh was aborted: Optional php installation failed to be configured!" | tee -a $log && exit 1
fi

# Configure Apache
if ! configure_apache; then
  2>&1 echo "  WARNING: Apache configuration failed. Apache may not work properly." | tee -a $log
fi

echo "END: php.sh" | tee -a $log
# END: MAIN