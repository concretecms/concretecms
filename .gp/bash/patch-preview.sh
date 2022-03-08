#!/bin/bash
# Hotfix for https://github.com/apolopena/gitpod-laravel-starter/issues/19
msg="\e[38;5;208mGitpod was not able to open the preview.\e[0m
You may open the preview by running the command:\e[38;5;183m op\e[0m
You may also pass in an additional path to open such as:\e[38;5;183m op phpmyadmin\e[0m"
echo -e "$msg"