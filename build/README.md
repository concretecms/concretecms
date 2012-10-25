# Building Assets Required for concrete5

## css.sh

### Requirements: You must have "lessc" installed and in your $PATH in order for this script to work. 

This shell script runs all .less files through lessc and places them where they need to be. It also minifies them.

## js.sh

### Requirements: You must have "uglify-js" installed and in your $PATH in order for this script to work. You must also be running a system where "cat" works from the command line.

This shell script runs all .js files in ccm_app/ and bootstrap and builds the proper ccm.app.js and bootstrap.js files.