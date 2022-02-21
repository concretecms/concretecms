#!/bin/bash
#
# SPDX-License-Identifier: MIT
# Copyright © 2021 Apolo Pena
#
# start-server.sh
# Description:
# Starts the default server once MySQL is ready.

# Load spinner
. .gp/bash/spinner.sh

if [[ $(bash .gp/bash/helpers.sh is_inited) == 1 ]]; then
  gp sync-done gitpod-inited
fi;
UP=$(pgrep mysql | wc -l)
if [[ $UP -ne 1 ]]; then
  start_spinner "Initializing MySql..." &&
  gp await-port 3306 &&
  stop_spinner $?
fi 
gp await-port 3306 &&
__port=$(bash .gp/bash/helpers.sh get_default_server_port)
__server=$(bash .gp/bash/utils.sh parse_ini_value starter.ini development default_server)
start_spinner "Starting $__server server on port $__port when system is ready..." &&
gp sync-await gitpod-inited &&
stop_spinner $? &&
bash -i .gp/bash/helpers.sh start_server
