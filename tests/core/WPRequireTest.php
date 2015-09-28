<?php
namespace WPRequireTest;

use WPRequire\WPRequire;
use WPRequire\lib\WPPlugin;

class WPRequireTest extends \WP_UnitTestCase {

    function testGetAllActivePlugins() {
        activate_plugin("akismet/akismet.php");

        $this->assertEquals(
            "akismet/akismet.php",
            WPRequire::getAllActivePlugins()[0]->getPluginFile()
        );

        $this->assertTrue(WPRequire::getAllActivePlugins()[0] instanceof WPPlugin);
    }
}

