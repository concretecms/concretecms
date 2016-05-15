[![Build Status](https://travis-ci.org/concrete5/concrete5.svg?branch=master)](https://travis-ci.org/concrete5/concrete5-tests)


## Step 1: Install PHPUnit

Install PHPUnit through Pear, MacPorts, or whatever system you use. It is available for [many packaging systems](http://phpunit.de/manual/current/en/installation.html). When done, you should be able to run "phpunit" from your command line and get a help response.


## Step 2: Clone this repository.

Or download it.


## Step 3: Setup concrete5

As described [here](../README.md#installation)


## Step 4: Setup the database

The test system expects to have access to a MySQL installation on the same computer where the tests will be executed.
The tests needs to have administration rights on MySQL in order to create and drop the test database and the tables inside it.
You need to create a MySQL account with login `travis` and an empty password:

```sql
CREATE USER 'travis'@'localhost' IDENTIFIED BY '';
GRANT ALL ON *.* TO 'travis'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
```


## Step 5: Run the tests!

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
