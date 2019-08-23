#!/bin/bash

set -o errexit

source "$( dirname "${BASH_SOURCE[0]}" )/travis_retry.sh"

echo 'Installing Grunt'
travis_retry npm -g install grunt

echo 'Installing Node packages'
cd "$TRAVIS_BUILD_DIR/build"
travis_retry npm install
