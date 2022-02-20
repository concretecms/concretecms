#!/bin/bash
#
# SPDX-License-Identifier: MIT
# Copyright © 2021 Apolo Pena
#
# init-rake-tasks.sh
# Description:
# Creates rake tasks dynamically.

parse="bash .gp/bash/utils.sh parse_ini_value starter.ini"

# BEGIN: dynamic rake task functions
add_changelog_rake() {
  local rake project since_tag future_release
  rake='changelog'
  project=$(basename "$GITPOD_REPO_ROOT")
  since_tag=$(eval "$parse" github-changelog-generator since_tag)
  future_release=$(eval "$parse" github-changelog-generator future_release)

  # this rake task cannot handle empty strings as values so handle them (whitespace is ok though)
  [ -z "$since_tag" ] && default_since_tag='' ||  default_since_tag="config.since_tag = '$since_tag'"
  [ -z "$future_release" ] && default_future_release='' ||  default_future_release="config.future_release = '$future_release'"

# Do not indent the below HEREDOC code block!
IFS='' read -r -d '' __task <<EOF
require 'github_changelog_generator/task'
GitHubChangelogGenerator::RakeTask.new :changelog do |config|
  config.user = '$GITPOD_GIT_USER_NAME'
  config.project = '$project'
  $default_since_tag
  $default_future_release
end
EOF

  bash .gp/bash/helpers.sh add_global_rake_task "$__task" "$rake"
}
# END: dynamic rake task functions
# BEGIN: add rake tasks
  if ! add_changelog_rake; then
    exit 1
  fi
# END: add rake tasks




