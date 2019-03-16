#!/bin/bash

set -o errexit

SCRIPT_DIR=$(dirname "${BASH_SOURCE[0]}")

source "$SCRIPT_DIR/travis_retry.sh"

echo 'Configuring PHP'
phpenv config-add "$SCRIPT_DIR/php.ini"
phpenv config-rm xdebug.ini || true

echo 'Installing Composer packages - Magento''s composer merger'
travis_retry composer update --no-suggest --no-interaction ${COMPOSER_FLAGS:-}

echo 'Installing Composer packages - Merged dependencies'
travis_retry composer update --no-suggest --no-interaction ${COMPOSER_FLAGS:-}
