#!/bin/bash
#
# SPDX-License-Identifier: MIT
# Copyright © 2021 Apolo Pena
#
# update-composer.sh
# Description:
# Programatically updates composer to the latest version.

LOG='/var/log/workspace-image.log'
# BEGIN: update composer to the latest version
echo "BEGIN: update composer" | tee -a $LOG
echo "  Purging existing version of composer: $(composer --version)" | tee -a $LOG
sudo apt-get --assume-yes purge composer
COMP_PURGE=$?
if [ $COMP_PURGE -ne 0 ]; then
  >&2 echo "  ERROR: failed to purge existing version of composer." | tee -a $LOG
else
  echo "  SUCCESS: purged existing version of composer." | tee -a $LOG
fi

echo "  Installing latest version of composer"  | tee -a $LOG
EXPECTED_CHECKSUM="$(wget -q -O - https://composer.github.io/installer.sig)"
sudo php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then
    >&2 echo "  ERROR: Invalid installer checksum - failed to install latest version of composer." | tee -a $LOG
    sudo rm composer-setup.php
else
  sudo php composer-setup.php --install-dir=/usr/bin --filename=composer
  COMP_VAL=$?
  if [ $COMP_VAL -ne 0 ]; then
    >&2 echo "  ERROR $COMP_VAL: Failed to install latest version of composer." | tee -a $LOG
  else
    echo "  SUCCESS: latest version of composer installed: $(composer --version)" | tee -a $LOG
  fi
  sudo rm composer-setup.php
fi

echo "END: update composer" | tee -a $LOG
# END: update composer to the latest version