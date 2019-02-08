#!/bin/bash

set -o errexit

SCRIPT_DIR=$(dirname "${BASH_SOURCE[0]}")

source "$SCRIPT_DIR/travis_retry.sh"

echo 'Configuring PHP'
phpenv config-add "$SCRIPT_DIR/php.ini"
phpenv config-rm xdebug.ini || true

FLAGS=''
if test -n "${COMPOSER_PREFER_LOWEST:-}"; then
    FLAGS="$FLAGS --prefer-lowest"
fi

echo 'Installing Composer packages - Magento''s composer merger'
travis_retry composer update --no-suggest --no-interaction $FLAGS

echo 'Installing Composer packages - Merged dependencies'
travis_retry composer update --no-suggest --no-interaction $FLAGS
