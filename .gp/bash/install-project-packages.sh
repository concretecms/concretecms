#!/bin/bash
# shellcheck disable=SC1091
#
# SPDX-License-Identifier: MIT
# Copyright © 2022 Apolo Pena
#
# install-project-packages.sh
# Description:
# Installs project specific packages as per starter.ini
#
# Notes:
# This script assumes it is being run from .gitpod.Dockerfile as a sudo user
# and that all of this scripts dependencies have already been copied to /tmp
# If you change this script you must force a rebuild of the docker image
#

packages="$(bash /tmp/utils.sh parse_ini_value /tmp/starter.ini apt-get packages)"
log='/var/log/workspace-image.log'
IFS=" " read -r -a all_packages <<< "$packages"

# Abort if $packages has no value set OR contains all whitespace
[[ -z ${packages// } ]] && exit 0

echo "BEGIN: Installing user specified project packages" | tee -a $log
echo 'debconf debconf/frontend select Noninteractive' | sudo debconf-set-selections \
  && sudo apt-get update -q \
  && sudo apt-get -yq install "${all_packages[@]}"
ec=$?
if [[ $ec -ne 0 ]]; then
  2>&1 echo "  ERROR: failed while installing one or more of the following user specified project packages: ${all_packages[*]}" | tee -a $log
else
  echo "  SUCCESS: Installing user specified project packages: ${all_packages[*]}" | tee -a $log
fi
echo "END: Installing user specified project packages" | tee -a $log