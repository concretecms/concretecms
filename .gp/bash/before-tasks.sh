#!/bin/bash
#
# SPDX-License-Identifier: MIT
# Copyright © 2021 Apolo Pena
#
# before-tasks.sh
# Description:
# Tasks that should be run every time the workspace is created or started.
# 
# Notes:
# Gitpod currently does not persist files in the home directory so we must write them 
# in everytime the workspace starts. This is done in the 'before' task in .gitpod.yml

# Load logger
. .gp/bash/workspace-init-logger.sh

# Rake tasks (will be written to ~/.rake).
# Some rake tasks are dynamic and depend on the configuration in starter.ini
if [[ $(bash .gp/bash/utils.sh parse_ini_value starter.ini github-changelog-generator install) ]]; then
  msg="Writing rake tasks"
  if bash .gp/bash/init-rake-tasks.sh; then 
    log_silent "SUCCESS: $msg"
  else
    log -e "ERROR: $msg"
  fi
fi
git status
# Aliases for git
msg="git aliases have been written"
bash .gp/bash/utils.sh add_file_to_file_after "\\[alias\\]" .gp/snippets/git/emoji-log ~/.gitconfig &&
bash .gp/bash/utils.sh add_file_to_file_after "\\[alias\\]" .gp/snippets/git/aliases ~/.gitconfig &&
log_silent "$msg" &&
log_silent "try: git a    or: git aliases to see what is available."

# BEGIN: Enable GPG key to sign Git commits.
# Error handling for improper use of GPG environment variables
err_msg_prefix1="A GPG_KEY was found but it's corresponding GPG_KEY_ID was not."
err_msg_prefix2="A GPG_KEY_ID was found but it's corresponding GPG_KEY was not."
err_msg_suffix="Git commits will not be signed."
[[ -n $GPG_KEY && -z $GPG_KEY_ID ]] &&
log -e "ERROR: $err_msg_prefix1 $err_msg_suffix"
[[ -n $GPG_KEY_ID && -z $GPG_KEY ]] &&
log -e "ERROR: $err_msg_prefix2 $err_msg_suffix"
# Main GPG key logic
if [[ -n $GPG_KEY && -n $GPG_KEY_ID ]]; then
  gpg_conf_path=~/.gnupg/gpg.conf
  msg="Enabling Git commit signing for GPG key id: $GPG_KEY_ID"
  gpg -q --batch --import <(echo "$GPG_KEY" | base64 -d) &&
  echo 'pinentry-mode loopback' >> "$gpg_conf_path" &&
  git config --global user.signingkey "$GPG_KEY_ID" &&
  git config commit.gpgsign true
  ec=$?
  if [[ $ec -eq 0 ]]; then 
    log_silent "SUCCESS: $msg"
    # Change the git email if the user needs it (ensures the commit is marked as 'Verified')
    if [[ -n $GPG_MATCH_GIT_TO_EMAIL ]]; then
      msg="Setting user.email in ~/.gitconfig to $GPG_MATCH_GIT_TO_EMAIL"
      if git config --global user.email "$GPG_MATCH_GIT_TO_EMAIL"; then
        log_silent "SUCCESS: $msg"
      else
        log -e "ERROR: $msg"
      fi
    fi
    # Ultimately trust the key, bump to lowercase and check the value of the directive
    if [[ $(echo "$GPG_AUTO_ULTIMATE_TRUST" | tr '[:upper:]' '[:lower:]') == yes ]]; then
      msg="Automagically giving ultimate trust to GPG_KEY_ID: $GPG_KEY_ID"
      # Prepend the key id as a trusted hex and update the local database with a silent arbitrary gpg call
      echo -e ""trusted-key 0x"$GPG_KEY_ID""\n$(cat $gpg_conf_path)" > "$gpg_conf_path" &&
      gpg --list-keys &> /dev/null
      ec=$?
      if [[ $ec -eq 0 ]]; then 
        log_silent "SUCCESS: $msg"
      else
        log -e "ERROR: $msg"
      fi
    fi
  else
    log -e "ERROR: $msg"
  fi
fi
# END: Enable GPG key to sign Git commits.

# Auto activate intelephense if license key is available
if [[ -n $INTELEPHENSE_LICENSEKEY ]]; then
  msg="creating $HOME/intelephense/licence.txt"
  log_silent "INTELEPHENSE_LICENSEKEY environment variable found, $msg"
  mkdir -p "$HOME/intelephense" &&
  echo "$INTELEPHENSE_LICENSEKEY" > "$HOME/intelephense/licence.txt" &&
  ec=$?
  if [[ $ec -eq 0 ]]; then 
    log "SUCCESS: $msg"
  else
    log -e "ERROR: $msg"
  fi
fi

# Restore files marked as persistant such as workspace-init.log
# See persist_file in bash/helpers.sh for how the system works
# Keep this block at the bottom of the file so that any logging from this
# script is only written to file upon initialization! Otherwise workspace-init.log 
# will get written to from this script upon every workspace restart.
if [[ $(bash .gp/bash/helpers.sh is_inited) == 1 ]]; then
  bash .gp/bash/helpers.sh restore_persistent_files "$GITPOD_REPO_ROOT"
fi
