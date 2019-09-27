#!/bin/bash

set -o errexit
set -o nounset

AUTO_REPOSITORY_OWNER='concrete5'
AUTO_REPOSITORY_NAME='concrete5'
AUTO_COMMIT_NAME_BASE='Automatic assets rebuilding'
AUTO_COMMIT_AUTHOR_NAME='concrete5 TravisCI Bot'
AUTO_COMMIT_AUTHOR_EMAIL='concrete5-bot@concrete5.org'
AUTO_COMMIT_NAME="[skip ci] $AUTO_COMMIT_NAME_BASE"

if test "${TRAVIS_PULL_REQUEST:-}" != 'false'; then
    printf '%s: skipping because it''s a pull request.\n' "$AUTO_COMMIT_NAME_BASE"
    exit 0
fi

if test -n "${TRAVIS_TAG:-}"; then
    printf '%s: skipping because the the current build is for a git tag.\n' "$AUTO_COMMIT_NAME_BASE"
    exit 0
fi

if test -z "${TRAVIS_BRANCH:-}"; then
    printf '%s: skipping because the current branch is not available.\n' "$AUTO_COMMIT_NAME_BASE"
    exit 0
fi

if test "${TRAVIS_REPO_SLUG:-}" != "$AUTO_REPOSITORY_OWNER/$AUTO_REPOSITORY_NAME"; then
    printf '%s: skipping because repository is "%s" instead of "%s/%s".\n' "$AUTO_COMMIT_NAME_BASE" "${TRAVIS_REPO_SLUG:-}" "$AUTO_REPOSITORY_OWNER" "$AUTO_REPOSITORY_NAME"
    exit 0
fi

if test "${TRAVIS_COMMIT_MESSAGE:-}" = "$AUTO_COMMIT_NAME"; then
    printf '%s: skipping because commit is already "%s".\n' "$AUTO_COMMIT_NAME_BASE" "$AUTO_COMMIT_NAME"
    exit 0
fi

if test -z "${GITHUB_ACCESS_TOKEN:-}"; then
    printf '%s: skipping because GITHUB_ACCESS_TOKEN is not available
To create it:
 - go to https://github.com/settings/tokens/new
 - create a new token
 - sudo apt update
 - sudo apt install -y build-essential ruby ruby-dev
 - sudo gem install travis
 - travis encrypt --repo %s/%s GITHUB_ACCESS_TOKEN=<YOUR_ACCESS_TOKEN>
 - Add to the env setting of:
   secure: "encrypted string"
' "$AUTO_COMMIT_NAME_BASE" "$AUTO_REPOSITORY_OWNER" "$AUTO_REPOSITORY_NAME"
    exit 0
fi

printf '%s: checking out %s\n' "$AUTO_COMMIT_NAME_BASE" "$TRAVIS_BRANCH"
cd "$TRAVIS_BUILD_DIR"
git checkout -qf "$TRAVIS_BRANCH"

printf '%s: building assets\n' "$AUTO_COMMIT_NAME_BASE"
cd "$TRAVIS_BUILD_DIR/build"
printf -- '- CSS\n'
grunt css:release
printf -- '- JS\n'
grunt js:release

printf '%s: checking changes\n' "$AUTO_COMMIT_NAME_BASE"
CHANGES_DETECTED=0
cd "$TRAVIS_BUILD_DIR/concrete/css"
if test -n "$(git status --porcelain .)"; then
    printf -- '- changes detected in CSS assets\n'
    git add --all .
    CHANGES_DETECTED=1
else
    printf -- '- no changes in CSS assets\n'
fi
cd "$TRAVIS_BUILD_DIR/concrete/js"
if test -n "$(git status --porcelain .)"; then
    printf -- '- changes detected in JS assets\n'
    git add --all .
    CHANGES_DETECTED=1
else
    printf -- '- no changes in JS assets\n'
fi

if test $CHANGES_DETECTED -eq 0; then
    printf '%s: skipping because assets are already up-to-date\n' "$AUTO_COMMIT_NAME_BASE"
    exit 0
fi

printf '%s: commiting and pushing changes.\n' "$AUTO_COMMIT_NAME_BASE"
cd "$TRAVIS_BUILD_DIR"
git status --short
git config user.name "$AUTO_COMMIT_AUTHOR_NAME"
git config user.email "$AUTO_COMMIT_AUTHOR_EMAIL"
git commit -m "$AUTO_COMMIT_NAME"
git remote add deploy "https://$GITHUB_ACCESS_TOKEN@github.com/$AUTO_REPOSITORY_OWNER/$AUTO_REPOSITORY_NAME.git"
git push deploy "$TRAVIS_BRANCH" -vvv
printf '%s: repository updated.\n' "$AUTO_COMMIT_NAME_BASE"
