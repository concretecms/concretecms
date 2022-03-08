# shellcheck shell=bash
# shellcheck disable=2142
# This file is sourced into ~/.bashrc
# Add any alias you would like here

# opens or refreshes the preview browser. Try op --help
alias op='f(){ bash "$GITPOD_REPO_ROOT"/.gp/bash/open-preview.sh "$@";  unset -f f; }; f'