<?php
/*
 * Plugin Name: WP Require
 * Plugin URI:  https://github.com/sigurdsvela/wp-require
 * Description: A WordPress plugin to handle plugin dependencies
 * Version:     0.1-alpha
 * Author:      Sigurd Svela
 * Author URI:  https://github.com/sigurdsvela
 * Text Domain: wp-require
 * Domain Path: /lang
 */

//Hot fix. Until phpstd implements composer autoloading.
require_once __DIR__ . "/vendor/sigurdsvela/std/autoloader.php";
require_once __DIR__ . "/core/autoload.php";
use WPRequire\WPRequire;

if (!defined('WP_REQUIRE_ABSPATH'))
    define('WP_REQUIRE_ABSPATH', __DIR__);

WPRequire::main();
