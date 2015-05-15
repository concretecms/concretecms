[![Build Status](http://img.shields.io/travis/concrete5/concrete5/develop.svg)](https://travis-ci.org/concrete5/concrete5-5.7.0)

# concrete5 Developer Repository

This is the developer repository for Concrete5.

## Version

This repository contains Concrete5 version 5.7 and greater. Look for 5.6? You want [http://github.com/concrete5/concrete5-legacy](the concrete5-legacy repository).

## Installation

1. Make sure your development environment enables PHP short tags.
2. Clone this repository.
3. Use [Composer](https://getcomposer.org/) to install the third party dependencies

    If composer is installed globally:

        cd web/concrete
        composer install

    If composer is downloaded as a .phar:

        cd web/concrete
        php /path/to/composer.phar install

    This should install everything necessary into the vendor/ directory in the concrete directory.

4. Use [npm](https://www.npmjs.org/) to install [grunt](http://gruntjs.com/) to the build directory and install the command line interface

        cd build
        npm install -g grunt-cli
        npm install

5. Build concrete5 sources with grunt

        cd build
        grunt release
