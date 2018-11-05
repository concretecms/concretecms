#!/bin/bash

set -o errexit

source "$( dirname "${BASH_SOURCE[0]}" )/travis_retry.sh"

echo 'Configuring PHP'
phpenv config-add "$( dirname "${BASH_SOURCE[0]}" )/php.ini"
if test $DO_PHPUNIT -lt 2; then
    phpenv config-rm xdebug.ini || true
fi

echo 'Installing Composer packages - Magento''s composer merger'
travis_retry composer update --no-suggest --no-interaction $PREFER_LOWEST

echo 'Installing Composer packages - Merged dependencies'
travis_retry composer update --no-suggest --no-interaction $PREFER_LOWEST
