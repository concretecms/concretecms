#!/bin/bash
#
# SPDX-License-Identifier: MIT
# Copyright © 2021 Apolo Pena
#
# allow-mixed-web.sh
# Description:
# Injects routes/web.php with a snippet that allows mixed web content
#
# Notes:
# Call this script from init-complete.sh to ensure the snippet will not be overwritten.

. .gp/bash/workspace-init-logger.sh
. .gp/bash/spinner.sh
laravel_web=routes/web.php
laravel_web_snippet=.gp/snippets/laravel/routes/web/allow-mixed-web.snippet
msg="Injecting $laravel_web file"
if [[ -e $laravel_web ]]; then
  [[ ! -e $laravel_web_snippet ]] && fail=1 && e_msg="Missing injection file $laravel_web_snippet"
  if ! grep -q "Injected from $laravel_web_snippet" "$laravel_web"; then
    log_silent "$msg" && start_spinner "$msg" && sleep .5
    cat "$laravel_web_snippet" >> "$laravel_web" 2> /dev/null
    err_code=$?
    if [[ $err_code -ne 0  || $fail -eq 1 ]]; then
      stop_spinner 1
      [[ $fail -eq 1 ]] && msg=$e_msg
      log_silent -e "ERROR: $msg"
    else
      stop_spinner 0
      log_silent "SUCCESS: $msg"
    fi # end check success or failure
  fi # end check if file is already injected
else
  log_silent "$msg" && start_spinner "$msg" && sleep .5 && stop_spinner 1
  log_silent -e "ERROR: no $laravel_web file to inject"
fi # end check injection file exists
