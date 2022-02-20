#!/bin/bash
#
# SPDX-License-Identifier: MIT
# Copyright © 2021 Apolo Pena
#
# init-react-typescript-example.sh
# Description:
# Initial setup for the gitpod-laravel-starter React Typescript example.

# Load logger
. .gp/bash/workspace-init-logger.sh

declare -a exit_codes=()

task_msg="Downloading React Typescript example: Questions and Answers"

log "$task_msg"
curl -LJO https://github.com/apolopena/qna-typescript-demo-skeleton/archive/refs/tags/1.1.1.tar.gz
exit_codes+=($?)
tar --overwrite -xvzf qna-typescript-demo-skeleton-1.1.1.tar.gz --strip-components=1
exit_codes+=($?)
rm qna-typescript-demo-skeleton-1.1.1.tar.gz
exit_codes+=($?)

if [[ $(echo "${exit_codes[@]}" | tr -d '[:space:]') =~ ^[0]+$ ]]; then
  log "SUCCESS: $task_msg"
else
  log -e "ERROR: $task_msg"
fi


