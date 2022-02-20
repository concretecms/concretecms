#!/bin/bash
#
# SPDX-License-Identifier: MIT
# Copyright © 2021 Apolo Pena
#
# init-react-example.sh
# Description:
# Initial setup for the gitpod-laravel-starter React example.

# Load logger
. .gp/bash/workspace-init-logger.sh

declare -a exit_codes=()
all_zeros='^[0]+$'
task_msg="Setting up React example: Questions and Answers"

log "$task_msg"
curl -LJO https://github.com/apolopena/qna-demo-skeleton/archive/refs/tags/1.1.1.tar.gz
exit_codes+=($?)
tar --overwrite -xvzf qna-demo-skeleton-1.1.1.tar.gz --strip-components=1
exit_codes+=($?)
rm qna-demo-skeleton-1.1.1.tar.gz
exit_codes+=($?)

if [[ $(echo "${exit_codes[@]}" | tr -d '[:space:]') =~ $all_zeros ]]; then
  log "SUCCESS: $task_msg"
else
  log -e "ERROR: $task_msg"
fi




