#!/bin/sh

set -o errexit

# Start MariaDB
ccm-service start db

# Reset previously installed concrete5
sudo -u www-data -- /app/concrete/bin/concrete5 c5:reset -f

# Re-install concrete5
sudo -u www-data -- /app/concrete/bin/concrete5 c5:install -vvv \
    --db-server=localhost \
    --db-username=c5 \
    --db-password=12345 \
    --db-database=c5 \
    --timezone=UTC \
    --site='concrete5 website' \
    --starting-point=elemental_full \
    --admin-email=admin@example.org \
    --admin-password=12345
