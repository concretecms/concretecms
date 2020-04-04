#!/bin/sh

set -o errexit

# Start MariaDB
ccm-service start db

# Update concrete5 to current version
sudo -u www-data -- /app/concrete/bin/concrete5 c5:update -vvv
