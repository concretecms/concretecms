#!/bin/sh

set -o errexit

# Set the currently active PHP version
if test -n "${CCM_TEST_PHPVERSION:-}"; then
    switch-php "$CCM_TEST_PHPVERSION"
fi

# Start MariaDB
ccm-service start db

# Reset previously installed concrete5
c5 c5:reset -f

# Output error information to site users
c5 c5:config -g -- set concrete.debug.display_errors true

# Show the debug error output
c5 c5:config -g -- set concrete.debug.detail debug

# Consider warnings as errors
c5 c5:config -g -- set concrete.debug.error_reporting -1

# Re-install concrete5
c5 c5:install -vvv \
    --db-server=localhost \
    --db-username=c5 \
    --db-password=12345 \
    --db-database=c5 \
    --timezone=UTC \
    --site='concrete5 website' \
    --starting-point=elemental_full \
    --admin-email=admin@example.org \
    --admin-password=12345
