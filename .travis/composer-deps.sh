#!/bin/bash

set -o errexit

echo 'Configuring PHP'
phpenv config-add "$( dirname "${BASH_SOURCE[0]}" )/php.ini"
phpenv config-rm xdebug.ini || true

echo 'Installing Composer packages - Magento's composer merger'
travis_retry composer update --no-suggest --no-interaction $PREFER_LOWEST 

echo 'Installing Composer packages - Merged dependencies'
travis_retry composer update --no-suggest --no-interaction $PREFER_LOWEST 
