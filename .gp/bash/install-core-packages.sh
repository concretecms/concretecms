#!/bin/bash
# shellcheck disable=SC1091
#
# SPDX-License-Identifier: MIT
# Copyright © 2022 Apolo Pena
#
# install-core-packages.sh
# Description:
# Installs package dependencies for gitpod-laravel-starter
#
# Notes:
# This script assumes it is being run from .gitpod.Dockerfile as a sudo user
# and that all of this scripts dependencies have already been copied to /tmp
# If you change this script you must force a rebuild of the docker image
#

log='/var/log/workspace-image.log'
php_version="$(. /tmp/utils.sh php_version)"
core='rsync grc shellcheck'

# Append the appropriate phpfpm package to core if the current php version is greater than 7.4
(( $(bc <<<"$php_version > 7.4") )) \
  && core="${core} php$php_version-fpm"

IFS=" " read -r -a all_packages <<< "$core"

echo "BEGIN: Installing core packages" | tee -a $log

echo 'debconf debconf/frontend select Noninteractive' | sudo debconf-set-selections \
  && sudo apt-get update -q \
  && sudo apt-get -yq install "${all_packages[@]}"
ec=$?
if [[ $ec -ne 0 ]]; then
  2>&1 echo "  ERROR: failed while installing one or more of the following core packages: ${all_packages[*]}" | tee -a $log
else
  echo "  SUCCESS: Installing core packages: ${all_packages[*]}" | tee -a $log
fi
echo "END: Installing core packages" | tee -a $log