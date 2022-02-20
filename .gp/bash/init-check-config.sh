#!/bin/bash
#
# SPDX-License-Identifier: MIT
# Copyright © 2021 Apolo Pena
#
# init-check-config.sh
# Description:
# Detects and logs any possible mismatched configuration values. mismatches can occur between 
# the docker image and starter.ini or laravel scaffolding files in version control, etc..

# BEGIN: Handle the cases of a Laravel version mismatch
if [[ -f ~/laravel-starter/artisan ]]; then
  [[ $(php ~/laravel-starter/artisan --version) =~ ([[:digit:]]+\.[[:digit:]]+\.[[:digit:]]+) ]] && cached_lv=${BASH_REMATCH[1]}
  [[ -z $cached_lv ]] && log -e "INTERNAL ERROR: could not derive laravel version from ~/laravel-starter"
  cached_major_lv=${cached_lv%%.*}
  # If composer.json is in VCS then assume Laravel scaffolding is in VCS so get the laravel version from parsing composer.json
  if git ls-files --error-unmatch composer.json > /dev/null 2>&1; then
    [[ $(grep laravel/framework composer.json) =~ ([[:digit:]]+\.) ]] && raw_lv=${BASH_REMATCH[1]}
    lv="${raw_lv%%.*}.*"
    in_vcs=1
  else
  # Otherwise this is a new project so get the laravel version from starter.ini
    lv=$(bash .gp/bash/utils.sh parse_ini_value starter.ini laravel version)
  fi
  # BEGIN: Detect and log a laravel version mismatch
  major_lv=${lv%%.*}
  if [[ $major_lv != "$cached_major_lv" ]]; then
    if [[ $in_vcs == 1 ]]; then
      msg1="ERROR: Laravel Version mismatch: Your laravel project scaffolding was built with version $lv but the laravel version used in the docker image is $cached_major_lv.*"
      msg2="Your project can be automatically converted from $lv to $cached_major_lv.* by running \"composer install\" but this is a potentially an unstable operation and is not advised."
      msg3="The safest way to fix this error is to:\n  1. Set the laravel version in starter.ini to $lv\n  2. Break the docker cache by incrementing INVALIDATE_CACHE in .gitpod.Dockerfile\n  3. Create a new workspace"
      msg4="Proceeding, but this error should be corrected immediately"
    else
      msg1="WARNING: Laravel Version mismatch: The laravel version set in starter.ini in the repository is $lv but the laravel version used in the docker image is $cached_major_lv.*"
      msg2="You need to break the docker image cache by incrementing INVALIDATE_CACHE in .gitpod.Dockerfile and then create a new workspace"
      msg3="WARNING: Proceeding using laravel version $cached_major_lv.* which is probably not what your were expecting"
    fi
    log -e "$msg1"
    log "$msg2"
    log "$msg3"
    [[ -n $msg4 ]] && log "$msg4"
  fi
  # END: Detect and log a laravel version mismatch
fi
# END: Handle the cases of a Laravel version mismatch