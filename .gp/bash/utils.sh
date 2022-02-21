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


# check_files_exist
# Description:
# Checks if any number of files exist.
# Exits with error code 1 on the first file that does not exist.
#
# Notes:
# Pass this function any number of files as arguments.
# File paths must be absolute or relative to this script (utils.sh)
# Error code 0 if all files exist
# Error code 1 if any file doesn't exist. Function exits on the first file that doesn't exist.
# When a file does not exist a message is echoed to the console.
#
# Usage:
# Example: check if any of these two files dont exist (assume they do exist)
# check_files_exist .bashrc .bash_profile
# outputs: all files exist
# if [ $? -eq 0 ]; then echo "all files exist"; fi
#
# Example: check if any of these three files dont exist (assume that foo.txt does not exist)
# check_files_exist .bashrc foo.txt .bash_profile
# outputs: the file foo.txt does not exist
# if [ $? -eq 0 ]; then echo "all files exist"; fi
check_files_exist () {
  for arg
  do if [[ ! -f $arg ]]; then echo ERROR: the file "$arg" does not exist; exit 1; fi
  done
}

# add_file_to_file_before
# Description:
# Adds the contents of file ($2) into another file ($3) before the marker ($1)
#
# Notes:
# The marker is a regexp expression so it must have any regexp characters in it double escaped.
#
# Usage:
# Example: add the contents of git-alises.txt to .gitconfig before the marker [aliases]
# add_file_to_file_before "\\[alias\\]" git-aliases.txt .gitconfig
#
add_file_to_file_before() {
  check_files_exist "$2" "$3" && local c=$?; if [[ $c -ne 0 ]]; then exit 1; fi
  awk "/$1/{while(getline line<\"$2\"){print line}} //" "$3" >__tmp && mv __tmp "$3"
}

# add_file_to_file_after
# Description:
# Adds the contents of file ($2) into another file ($3) after the marker ($1)
#
# Notes:
# The marker is a regexp expression so it must have any regexp characters in it double escaped.
#
# Usage:
# Example: add the contents of git-alises.txt to .gitconfig after the marker [aliases]
# add_file_to_file_after \\[alias\\] git-aliases.txt .gitconfig
#
add_file_to_file_after() {
  check_files_exist "$2" "$3" && local c=$?; if [ $c -ne 0 ]; then exit 1; fi
  awk "//; /$1/{while(getline<\"$2\"){print}}" "$3" >__tmp && mv __tmp "$3"
}

# parse_ini_value
# Description:
# Echoes the value of a variable ($3) for a name value pair under a section ($2) in an .ini file ($1)
#
# Notes:
# Comments are ignored.
# Comments are either a pound sign # or a semicolon ; at the beginning of a line.
# The name argument ($3) and the section argument ($2)
# must be simple strings with no special regex characters in them.
# If a value is not set then an empty string with be echoed.
#
# Usage:
# Example: get the value of the install variable under the section myphpadmin in the file starter.ini
# Assume the contents of starter.ini has at least this block in it.
#   [phpmyadmin]
#   ; this is a comment: install=1, do not install = 0
#   install=1
#
# parse_ini_value starter.ini phpmyadmin install
# // outputs: 1
#
parse_ini_value() {
  sed -nr '/^#|^;/b;/\['"$2"'\]/,/\[.*\]/{/\<'"$3"'\>/s/(.*)=(.*)/\2/p}' "$1"
}

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

# node_package_exists
# Description:
# Checks is a node package directory ($1) is present on the file system
# An optional argument ($2) of the location to make the check can be passed
# otherwise the current working directory will be searched
# Echos 1 if a directory with the name of the node package ($1) exists within
# a directory named node_modules
# Echos 0 if a directory with the name of the node package ($1) does not exists within
# a directory named node_modules or if the node_modules directory does not exist
#
# Notes:
# If using the optional location argument ($2), make sure the path is valid
# or you can get a false negative
#
# Usage:
# Example 1:
# # Check if the node package react exists in the current working directory (assume that is does)
# node_package_is_installed react # echos 1
# Example 2:
# # Check if the node package foobar exists in the current working directory (assume that is does not)
# node_package_is_installed foobar # echos 0
# Example 3:
# # Check if the node package react exists in the foo/bar/baz (assume that is does)
# node_package_is_installed react foo/bar/baz # echos 1

node_package_exists () {
  [[ -z $1 ]] && echo "0" && exit 1
  local path
  path="$(pwd)/${2}/node_modules"
  [[ -z $2 ]] && path="$(pwd)/${2}node_modules"
  [[ -d $path/$1 ]] && echo "1" || echo "0"
}

# generate_string
# Description:
# Generates a string of random alphanumeric and special charaters of any length ($1)
# The length of the string defaults to 32 if no argument is passed in
# or if the argument passed in is empty or not a valid positive integer
#
# Usage:
# Example 1: generate a random string with a length of 8
# generate_string 8
#
# Example 2: generate a random string with a length of 32
# generate_string
#
generate_string () {
  local count=32
  [ "$1" -ge 0 ] 2>/dev/null && count=$1
  tr -dc 'a-zA-Z0-9$+,:;=?|<>.^*()%-' < /dev/urandom | fold -w "$count" | head -n 1
}

# get_env_value
# Description:
# Get the value of a key ($1) value pair as set in a env style file ($2)
# If no file ($2) argument is given then the file .starter.env will be used
# 
# Exit codes:
# 3 --> File ($2 or the default .starter.env) does not exist
# 4 --> Variable ($1) does not exist in file ($2 or the default .starter.env)
# 5 --> Value for the variable ($2) was not set or contained only whitespace
#
# Usage:
# Example 1 (with optional error handling): 
# # Get the value of PHP_PW from .starter.env, assuming the file contains PHP_PW=secret007
# # If there are no errors then the output would be secret007
# err="get_env_value Error:"
# value="$(get_env_value PHP_PW)"
# case "$?" in
#   '0')
#     echo $value
#     ;;
#   '3')
#     echo "$err No file .starter.env"
#     ;;
#   '4')
#     echo "$err No variable: PHP_PW"
#     ;;
#   '5')
#     echo "$err No value for variable: PHP_PW"
#     ;;
#   *)
#     echo "$err unidentified exit code: $?"
#     ;;
# esac 
#
# Example 2 (no error handling): 
# # Get the value of FOO from .bar (assume the file .bar contains FOO=foobarbaz)
# # If there are no errors the output would be foobarbaz, otherwise there would be no output.
# get_env_value FOO .bar
# 
get_env_value() {
  local file="$2"
  [ -z "$2" ] && file='.gp/.starter.env'
  [ ! -f "$file" ] && exit 3
  [ -z "$1" ] && exit 4
  grep -q "$1=" "$file"; local c=$? && [[ $c != 0 ]] && exit 4
  local val
  val=$(grep "$1=" "$file" | cut -d '=' -f2)
  [[ -z $val ]] && exit 5
  echo "$val"
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
