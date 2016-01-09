[![Build Status](http://img.shields.io/travis/concrete5/concrete5/develop.svg)](https://travis-ci.org/concrete5/concrete5)

Welcome to the official repository for concrete5 development! concrete5 is an open source CMS built by people from 
around the world. Want to get involved? Check out our [contributor guide](https://github.com/concrete5/concrete5/blob/develop/CONTRIBUTING.md) for more info.

## Activity
[![Throughput Graph](https://graphs.waffle.io/concrete5/concrete5/throughput.svg)](https://waffle.io/concrete5/concrete5/metrics)

## Documentation

If you're looking for concrete5 documentation, you'll want to navigate over to [documentation.concrete5.org](https://documentation.concrete5.org). 
If you see anything that needs more information or is just completely wrong, contributions are welcomed! 
Just log in to the documentation site with your concrete5.org account and edit away!

## Installation

1. Make sure your development environment enables PHP short tags.
2. Clone the repository

        git clone https://github.com/concrete5/concrete5.git
        cd concrete5/

3. Use [Composer](https://getcomposer.org/) to install the third party dependencies in `/concrete`

        cd concrete
        composer install
        cd ../

4. Use [npm](https://www.npmjs.org/) to install [grunt](http://gruntjs.com/) to the build directory and install the command line interface in `/build`

        cd build
        npm install -g grunt-cli
        npm install
        cd ../

## Legacy

Looking for legacy versions of concrete5? Head over to [the concrete5-legacy repository](http://github.com/concrete5/concrete5-legacy).
