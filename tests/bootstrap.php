<?php
error_reporting(E_ALL);

require_once __DIR__ . "/../vendor/autoload.php";

require_once __DIR__ . "/../core/autoload.php";
require_once __DIR__ . "/WPRequireTestUtils.php";

$_tests_dir = __DIR__ . '/wordpress-test-library';

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
    require dirname( dirname( __FILE__ ) ) . '/wp-require.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
