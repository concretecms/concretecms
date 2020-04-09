#!/bin/bash

set -o errexit

source "$( dirname "${BASH_SOURCE[0]}" )/travis_retry.sh"

cd "$TRAVIS_BUILD_DIR/build"

case "$TRAVIS_EVENT_TYPE" in
    push)
        echo "Checking out branch '$TRAVIS_BRANCH'"
        git checkout -qf "$TRAVIS_BRANCH"
        echo 'Installing Node packages as per package-lock.json file'
        travis_retry npm ci
        echo 'Updating bedrock to the latest commit in the default branch'
        npm install https://github.com/concrete5/bedrock.git
        ;;
    pull_request)
        echo 'Installing Node packages as per package-lock.json file'
        travis_retry npm ci
        PULL_REQUEST_AUTHOR="${TRAVIS_PULL_REQUEST_SLUG%%/*}"
        printf "Check if there's a '%s' branch for a bedrock repository owned by user '%s'... " "$TRAVIS_PULL_REQUEST_BRANCH" "$PULL_REQUEST_AUTHOR"
        if test -n "$(git ls-remote --heads "https://github.com/$PULL_REQUEST_AUTHOR/bedrock.git" "$$TRAVIS_PULL_REQUEST_BRANCH")"; then
            printf 'found! Using it.\n'
            npm install "https://github.com/$PULL_REQUEST_AUTHOR/bedrock.git#$TRAVIS_PULL_REQUEST_BRANCH"
        else
            printf 'not found.\n'
        fi
        ;;
esac

echo 'Installing Grunt'
travis_retry npm -g install grunt
