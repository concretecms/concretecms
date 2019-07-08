#!/bin/sh

set -o nounset
set -o errexit

if test "${TRAVIS_PULL_REQUEST:-}" != 'false'; then
    COMMIT_RANGE="$TRAVIS_COMMIT_RANGE"
else
    COMMIT_RANGE='HEAD~..HEAD'
fi

cd "$TRAVIS_BUILD_DIR"

IFS='
'
CHANGED_FILES=$(git diff --name-only --diff-filter=ACMRTUXB "$COMMIT_RANGE")

if test -z "$CHANGED_FILES"; then
    echo 'No changed files detected.'
else
    echo 'Checking the PHP coding style of the following changed files:'
    echo "$CHANGED_FILES"
    ./concrete/bin/concrete5 c5:phpcs --no-cache -- check ${CHANGED_FILES}
fi
