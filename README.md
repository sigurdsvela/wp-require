[![travis](https://travis-ci.org/sigurdsvela/wp-require.svg)](https://travis-ci.org/sigurdsvela/wp-require)

# WP Require

![Screen Shot](http://sigurdsvela.github.io/wp-require/res/screen-shot-1.png)

## What is wp-require
WP Require is a plugin for anybody who has been in the situation where a plugin requires another plugin, and causes the ever so lovley whitescreen of death, or some other fatal error that forces you to somehow deactivate th eplugin manually.

WP Require aims to fix that, and some other stuff while its at it

## Auto-Deactivate
If a plugin or theme requires something that is not provided by the environment, WP Require will automatically deactivate the plugin, and provide a usefull message that contains all the information about exaclty why it was deactivated.

## Auto Plugin Load Ordering
Let's say you have 3 plugins; plugin `A`, `B` and `C`. <br>`C` requires `B`, and `A` requires `B`.
<pre>
     Plugin B        
      ^     ^        
     /       \        
Plugin A  Plugin C        
</pre>

### Load Order
<pre>
With WP-Require | Without WP-Require (Alphabetical)
Plugin B        | Plugin A
Plugin A and C  | Plugin B
                | Plugin C
</pre>

> Without WP-Require, you will recive a
> 'function does not exists' error or similar
> when `Plugin A` is loaded, as it requires
> functionality from `Plugin B`. WP-Require
> makes sure to load them in the correct order.

Every plugin requires B. Now, WordPress doesn't know which plugin uses functionality from other plugins, and will load the plugins in an arbitrary fashion.
WP Require on the other hand knows, and will force the plugin order to make sure all of a plugins requirements have been loaded before the plugin is loaded.

## The wp-require.json file
The wp-require.json is placed in the root of your theme or plugin.

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
To contribute you will have to be able to run a MySQL database, and you need PHPUnit to run the unit tests.
Remember, no pull request will be accepted without unittests for said feature.

### Contribution workflow

0. Fork the project
1. Create a feature branch for your new feture.
2. When your done, merge it into the develop branch **with --no-ff**
3. Send a pull request.
