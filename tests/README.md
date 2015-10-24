[![Build Status](https://travis-ci.org/concrete5/concrete5.svg?branch=master)](https://travis-ci.org/concrete5/concrete5-tests)


## Step 1: Install PHPUnit

Install PHPUnit through Pear, MacPorts, or whatever system you use. It is available for [many packaging systems](http://phpunit.de/manual/current/en/installation.html). When done, you should be able to run "phpunit" from your command line and get a help response.


## Step 2: Clone this repository.

Or download it.


## Step 3: Install concrete5

Configure a web server to point to the `web` directory of your copy of the repository, and install concrete5, choosing the option to install `Sample Content with Blog`.
Do __NOT__ link to a production version because it's possible that some files or database values may be modified.


## Step 4: Run the tests!

For example

	cd tests
	phpunit

Expected output is something like

	PHPUnit 3.7.28 by Sebastian Bergmann.

	Configuration read from .../tests/phpunit.xml

	....................IIIIIII.IIIIIIIII..IIIIIIIII.IIIII.IIIIIIII  63 / 177 ( 35%)
	II.IIII.IIIIIIIIIII.IIIII.IIIIIII.II....II..II.........IIIIIIII 126 / 177 ( 71%)
	IIIIIIIIII.........................................

	Time: 12.2 seconds, Memory: 29.25Mb

	OK, but incomplete or skipped tests!
	Tests: 177, Assertions: 457, Incomplete: 91.


# Write Tests!

Send us tests via pull request.
