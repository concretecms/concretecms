#!/bin/sh

set -o errexit

# Set the currently active PHP version
if test -n "${CCM_TEST_PHPVERSION:-}"; then
    switch-php "$CCM_TEST_PHPVERSION"
fi

# Start MariaDB
ccm-service start db

# Output error information to site users
c5 c5:config -g -- set concrete.debug.display_errors true

# Show the debug error output
c5 c5:config -g -- set concrete.debug.detail debug

# Consider warnings as errors
c5 c5:config -g -- set concrete.debug.error_reporting -1

# Update concrete5 to current version
c5 c5:update -vvv
