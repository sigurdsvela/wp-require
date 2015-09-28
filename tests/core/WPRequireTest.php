<?php
namespace WPRequireTest;

use WPRequire\WPRequire;
use WPRequire\lib\WPPlugin;
use WPRequireTest\util\WPRequireTestUtils;

class WPRequireTest extends \WP_UnitTestCase {

    function testGetAllActivePlugins() {
        activate_plugin("akismet/akismet.php");

        /* To invoke the PRIVATE static method "getAllActivePlugins" */
        $WPRequire = new WPRequire();
        $activePlugins = WPRequireTestUtils::invokeMethod($WPRequire, "getAllActivePlugins");

        $this->assertEquals(
            "akismet/akismet.php",
            $activePlugins[0]->getPluginFile()
        );

        $this->assertTrue($activePlugins[0] instanceof WPPlugin);

        deactivate_plugins("akismet/akismet.php");
    }

    function testThatAddAdminNoticesThrowsExceptionIfTypeIsOutOfBounds() {
         /* To invoke the PRIVATE static method "getAllActivePlugins" */
        $WPRequire = new WPRequire();
        $this->setExpectedException('InvalidArgumentException');
        WPRequireTestUtils::invokeMethod($WPRequire, "addAdminNotice", ["the notice text", "not-valid"]);
    }
}

