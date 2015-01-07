[![Build Status](http://img.shields.io/travis/concrete5/concrete5-5.7.0/master.svg)](https://travis-ci.org/concrete5/concrete5-5.7.0)

# concrete5 5.7.0 Developer Repository

This is the developer repository for the 5.7 version of concrete5. In addition to user facing improvements (documented on concrete5.org), 5.7.0 is much different under the hood than 5.6 and earlier. In this repository we can make sure that 5.7 gets the attention it deserves, and anyone who wants to contribute to active 5.7 development can do so.

## Important

This is a developer preview branch. This is intended for Add-On developers and core hackers. This is not intended for production sites of any kind.

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
