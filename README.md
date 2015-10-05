![travis](https://travis-ci.org/sigurdsvela/wp-require.svg)

# WP Require

## What is wp-require
WP require is a plugin for WordPress developers. It allows you to set a
wp-require.json file in your theme or plugin
where you can specify which php version your require, what version of WordPress
you require, and what other plugins are required.

What does it do with this infomration? well, glad you asked.
It will make sure that, at any time, if a plugin or theme will not be able to
run. It is deactivated with a notice
to avoid wordpresses white screen of death.

It will also try to order the loading of plugins to make sure
that requirements are loaded before the plugin requiring them is loaded.

## The wp-require.json file
The wp-require file is written in either JSON.

Here is a sample file.
```javascript
{
	"php" : "5.3.*",
	"wordpress" : "4.*",
	"plugins" : {
		"plugin-name/plugin-name.php" : "1.0.*"
	}
}
```

Just put a file like this in the root of your theme or plugin. Make sure you have the wp-require plugin running, and BAM! You're done!


## Contributing

This project requires
- The wordpress test library, to be located in /tmp/wordpress-tests-lib/
- PHPUnit, google it. To run unit tests.
- wp-cli, again google it. This one is just to make your life simpler.

Use the wp-cli to set up the unit tests. This should also download the Wordpress
Test Library into the /tmp/ path. This library provides some usfull functionality, just
checkout the API. Now, inside of this library is a wp-test-config.php file that takes presidence over
the config file in your instalation when running unit tests. Remember to set the correct DB info in here.

Also remember, that if you are running PHPUnit with a PHP version that has not the default socket type set
to the MySQL running, you must use 127.0.0.1 address, and not "localhost". This will, for example, be relevant
if you are running MAMP or AMPPS, and using the built in php version supplied by your OS.

### Contribution workflow

0. Fork the project
1. Create a feature branch for your new feture.
2. When your done, merge it into the develop branch **with --no-ff**
3. Send a pull request.