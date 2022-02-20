#!/bin/bash
#
# SPDX-License-Identifier: MIT
# Copyright © 2021 Apolo Pena
#
# change-passwords.sh
# Description:
# Changes passwords for phpmyadmin from the defaults in version control to the values set in .starter.env
#
# Notes:
# This script should be always run at least once by the user as an mandatory additional layer of security
# This script requires the file .starter.env to exist along will all the key value pairs set.
#
# See:
# example.starter.ini

# Load spinner
. .gp/bash/spinner.sh

phpmyadmin() {
  [ ! -d public/phpmyadmin ] && echo "No installation of phpmyadmin was found. Process aborted. " && exit 1
  # Keep keys in sequence. Add new keys to the end of the array
  local keys=(PHPMYADMIN_SUPERUSER_PW PHPMYADMIN_CONTROLUSER_PW)

  local name="change-passwords.sh phpmyadmin"
  local err="$name ERROR:"
  local config_file="public/phpmyadmin/config.inc.php"
  local all_zeros='^[0]+$'
  local exit_codes
  local values

  for key in "${keys[@]}"; do
    local value
    value="$(bash .gp/bash/helpers.sh get_starter_env_val "$key")"
    values+=("$(bash .gp/bash/helpers.sh get_starter_env_val "$key")")
    local code=$?
    exit_codes+=$code
    # show error message of called function
    [ $code != 0 ] && echo "$value"
  done

  if [[ ! $(echo "${exit_codes[@]}" | tr -d '[:space:]') =~ $all_zeros ]]; then
    echo "$err retrieving values, no passwords were changed."
    exit 1
  fi

  # Values have been set and there are no errors so far so change passwords
  i=0
  for key in "${keys[@]}"; do
    case $key in
      "${keys[0]}")
        msg="Changing password for phpmyadmin user 'pmasu' to the value found in .starter.env"
        start_spinner "$msg"
        mysql -e "ALTER USER 'pmasu'@'%' IDENTIFIED BY '${values[$i]}'; FLUSH PRIVILEGES;"
        stop_spinner $?
        ;;
      "${keys[1]}")
        msg="Changing password for phpmyadmin user 'pma' to the value found in .starter.env"
        start_spinner "$msg"
        mysql -e "ALTER USER 'pma'@'localhost' IDENTIFIED BY '${values[$i]}'; FLUSH PRIVILEGES;"
        err_code=$?
        stop_spinner $err_code
        if [ $err_code == 0 ]; then
          msg="Updating control user password in $config_file"
          line="\$cfg['Servers'][\$i]['controlpass'] ="
          _edit="\$cfg['Servers'][\$i]['controlpass'] = '${values[$i]}';"
          start_spinner "$msg"
          # The shellcheck SC2026 was not designed for awk so bypass it
          # shellcheck disable=2026
          # Match the line where the password for the controluser is set
          line_num=$(awk '/^\$cfg.*'controlpass'.*=.*;$/ {print FNR}' $config_file)
          if [ -z "$line_num" ]; then
            stop_spinner 1
            echo -e "ERROR: No line found beginning with: $line \n\tin the file: $config_file"
            echo "You will need to manually update the control user password in $config_file"
          else
            sed -i "$line_num c\\$_edit" $config_file
            err_code=$?
            stop_spinner $err_code
            unset _edit
            [ $err_code == 0 ] &&
            echo -e "\e[38;5;171mPROCESS COMPLETE\e[0m" &&
            echo -e "\e[1;33mCheck the console output for any possible failures.\e[0m" &&
            echo -en "\e[1;36m" &&
            echo "If you are logged into phpmyadmin, log out and log back in." &&
            echo "For additional security you can delete .starter.env" &&
            echo "Just make sure you remember your passwords from that file." &&
            echo "If you ever loose your passwords you may always set them again" &&
            echo "using this script and new values set in .starter.env" &&
            echo -e "\e[0m"
          fi
        fi
        ;;
      *)
        echo "$err unidentified key $value"
        ;;
    esac
    ((i++))
  done
}

# Call functions from this script gracefully
if declare -f "$1" > /dev/null
then
  # call arguments verbatim
  "$@"
else
  echo "utils.sh: '$1' is not a known function name." >&2
  exit 1
fi
