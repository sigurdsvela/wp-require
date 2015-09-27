This README deserves some refining, yeah, this is just temporary.

This project requires
- The wordpress test library, to be located in /tmp/wordpress-tests-lib/
- PHPUnit, google it. To run unit tests.
- wp-cli, again google it. to run unit tests.

Use the wp-cli to set up the unit tests. This should also download the Wordpress
Test Library into the /tmp/ path. This library provides some usfull functionality, just
checkout the API. Now, inside of this library is a wp-test-config.php file that takes presidence over
the config file in your instalation when running unit tests. Remember to set the correct DB info in here.

Also remember, that if you are running PHPUnit with a PHP version that has not the default socket type set
to the MySQL running, you must use 127.0.0.1 address, and not "localhost". This will, for example, be relevant
if you are running MAMP or AMPPS, and using the built in php version supplied by your OS.