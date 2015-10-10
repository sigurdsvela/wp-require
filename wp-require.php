<?php
/*
 * Plugin Name: WP Require
 * Plugin URI:  https://github.com/sigurdsvela/wp-require
 * Description: Handles WordPress plugin and theme requirements.
 * Version:     0.1.1
 * Author:      Sigurd Svela
 * Author URI:  https://github.com/sigurdsvela
 * Text Domain: wp-require
 * Domain Path: /lang
 */

// We need some function from here
if (is_admin())
    require_once ABSPATH . "/wp-admin/includes/plugin.php";

// Hot fix. Until phpstd implements composer autoloading.
require_once __DIR__ . "/vendor/autoload.php";

require_once __DIR__ . "/core/autoload.php";

use WPRequire\WPRequire;

if (!defined('WP_REQUIRE_ABSPATH'))
    define('WP_REQUIRE_ABSPATH', __DIR__);

WPRequire::main();
