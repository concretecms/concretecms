#!/bin/sh

set -o errexit

# Start MariaDB
ccm-service start db

# Update concrete5 to current version
if sudo -u www-data -- /app/concrete/bin/concrete5 c5:update -vvv; then
    exit 0
fi

# We may need to restart the database
ccm-service restart db
sudo -u www-data -- /app/concrete/bin/concrete5 c5:update -vvv
