#!/bin/bash
#
# SPDX-License-Identifier: MIT
# Copyright © 2021 Apolo Pena
#
# utils.sh
# Description:
# A variety of useful bash functions with no dependecies.
#
# Notes:
# Do not execute this script without calling a function from it.
#
# Usage: bash <function name> arg1 arg2 arg3 ...

# log
# Description:
# Log a message ($1) to the console and an output file ($2).
# Logs to stdout and stderr if the -e or --error option is passed in as ($1) then
# the parameters shift and the message becomes ($2) and the output file becomes ($3)
#
# Notes:
# The output file must already exist.
# Backslash escapes are interpreted in both the console
# and the output file (e.g., they are not printed literally).
#
# Usage:
# Example 1: log a standard message an output file
# log_silent "Hello World" /var/log/test.log
#
# Example 2: log an error message an output file
# log_silent -e "Hello World" /var/log/test.log
#
log () {
  if [[ "$1" == '-e' || "$1" == '--error' ]]; then
    >&2 echo -e "$2" && printf -- '%b\n' "$2" >> "$3"
  else
    echo -e "$1" && printf -- '%b\n' "$1" >> "$2"
  fi
}

# log_silent
# Description:
# Log a message ($1) to an output file ($2).
# If the -e or --error option is passed in as ($1) then the parameters shift 
# and the message becomes ($2) and the output file becomes ($3)
#
# Notes:
# Backslash escapes are interpreted in the output file (e.g., they are not printed literally).
#
# Usage:
# Example 1: log a standard message to an output file
# log "Hello World"
#
# Example 2: log an error message to an output file
# log "Hello World" -e
#
log_silent () {
  if [[ "$1" == '-e' || "$1" == '--error' ]]; then
    1>&2 printf -- '%b\n' "$2" >> "$3"
  else
    printf -- '%b\n' "$1" >> "$2"
  fi
}

# php_version()
# Description:
# Gets the major and minor version of PHP from the installed configuration file php.ini
# Fails if PHP is not installed 
#
# Notes:
# Assumes that the file path for the loaded php.ini file is somewhat standard in that the first
# directory in the path with numbers with dots in them that resembles a version number is
# indeed the PHP version
#
# Usage:
# outputs x.y where x is the major PHP version and y in the minor PHP version: 
# php_version
#
php_version() {
  php --ini | head -n 1 | grep -Eo "([0-9]{1,}\.)[0-9]{1,}"
}

# split_ver
# Description:
# splits a version number ($1) into three numbers delimited by a space
#
# Notes:
# Assumes the format of the version number will be:
# <any # of digits>.<any # of digits>.<any # of digits>
#
# Usage:
# split_ver 6.31.140
# # outputs: 6 31 140 
split_ver() {
  local first=${1%%.*} # Delete first dot and what follows
  local last=${1##*.} # Delete up to last dot
  local mid=${1##$first.} # Delete first number and dot
  mid=${mid%%.$last} # Delete dot and last number
  echo "$first $mid $last"
}


# comp_ver_lt
# Description:
# Compares version number ($1) to version number ($2)
# Echos 1 if version number ($1) is less than version number ($2)
# Echos 0 if version number ($1) is greater than or equal to version number ($2)
#
# Notes:
# Assumes the format of the version number will be:
# <any # of digits>.<any # of digits>.<any # of digits>
#
# Usage:
# comp_ver_lt 2.28.10 2.28.9
# # outputs: 1
# comp_ver_lt 0.0.1 0.0.0
# # outputs: 0
comp_ver_lt() {
  local v1=()
  local v2=()
  IFS=" " read -r -a v1 <<< "$(split_ver "$1")"
  IFS=" " read -r -a v2 <<< "$(split_ver "$2")"
  [[ ${v1[0]} -lt ${v2[0]} ]] && echo 1 && exit
  [[ ${v1[0]} -eq ${v2[0]} ]] && \
  [[ ${v1[1]} -lt ${v2[1]} ]] && echo 1 && exit
  [[ ${v1[0]} -eq ${v2[0]} ]] && \
  [[ ${v1[1]} -eq ${v2[1]} ]] && \
  [[ ${v1[2]} -lt ${v2[2]} ]] && echo 1 && exit
  echo 0
}

# test_comp_ver_lt
# Description:
# test cases for comp_ver_lt
#
# Usage:
# test_comp_ver_lt
# outputs
# 0.0.0 is less than 0.0.1  true
# 1.0.0 is less than 1.0.0  false
# 0.0.1 is less than 0.0.2  true
# 1.0.1 is less than 1.1.0  true
# 2.0.1 is less than 2.0.2  true
# 3.99.1 is less than 98.0.0  true
# 6.1.3 is less than 6.1.1  false
# 2.2.2 is less than 2.2.2  false
# 0.33.33 is less than 0.33.33  false
# 0.33.33 is less than 0.33.32  false
# 0.0.44 is less than 0.0.45  true
test_comp_ver_lt() {
  local v1s=(0.0.0 1.0.0 0.0.1 1.0.1 2.0.1 3.99.1 6.1.3 2.2.2 0.33.33 0.33.33 0.0.44)
  local v2s=(0.0.1 1.0.0 0.0.2 1.1.0 2.0.2 98.0.0 6.1.1 2.2.2 0.33.33 0.33.32 0.0.45)
  [ "${#v1s[@]}" -ne "${#v2s[@]}" ] && echo "Error: test arrays do not match in length." && exit 1
  i=0
  for v1a in "${v1s[@]}"; do
    local v1b=${v2s[i]}
    [[ $(comp_ver_lt "$v1a" "$v1b") == 0 ]] && tf=false || tf=true
    echo "$v1a is less than $v1b  $tf"
    ((i++))
  done
}

# Trims all leading and trailing whitespace
trim_external() {
    local var="$*"
    # remove leading whitespace characters
    var="${var#"${var%%[![:space:]]*}"}"
    # remove trailing whitespace characters
    var="${var%"${var##*[![:space:]]}"}"   
    printf '%s' "$var"
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
