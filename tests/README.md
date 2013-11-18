[![Build Status](https://travis-ci.org/concrete5/concrete5-tests.png?branch=master)](https://travis-ci.org/concrete5/concrete5-tests)

# Step 1: Install PHPUnit

Install PHPUnit through Pear, MacPorts, or whatever system you use. It is available for many packaging systems. When done, you should be able to run "phpunit" from your command line and get a help response.

# Step 2: Clone this repository.

Or download it.

# Step 3: Add concrete5

Link to an existing installation of concrete5 (*), e.g.:

    cd concrete5-tests
    ln -s /Library/WebServer/WebSites/c541/web/ ./web

(*) Do NOT link to a production version because it's possible some files or database values may be modified.
You can download the latest development version from http://github.com/concrete5/concrete5/ and create the link as described above, but you must go through the c5 install routines before testing.

# Step 4: Run tests using bootstrap.php. For example

    phpunit --bootstrap tests/bootstrap.php tests
    
Expected output is something like

	PHPUnit 3.5.13 by Sebastian Bergmann.
	
	.............
	
	Time: 0 seconds, Memory: 15.25Mb
	
	OK (13 tests, 128 assertions)

# Write Tests!

Send us tests via pull request, or send @aembler a private message if you'd like direct commit access to this repository.
