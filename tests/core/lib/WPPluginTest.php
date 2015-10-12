<?php
namespace WPRequireTest\lib;

use WPRequire\WPRequireFile;
use WPRequireTest\util\WPRequireTestUtils;

class WPPluginTest extends \WP_UnitTestCase {

    public function testGetWpRequireFile() {
        $plugin = WPRequireTestUtils::createMockPlugin(
            array(
                "php" => "1.0.0",
                "wordpress" => "4.3",
                "plugins" => array(
                    "myplugin/myplugin.php" => "1.0.0"
                )
            )
        );
        $require = $plugin->getWpRequire();

        $this->assertTrue($require instanceof WPRequireFile);

        $this->assertEquals(
            "1.0.0",
            (string)$require->getRequiredPhpVersion()
        );

        $this->assertEquals(
            "4.3.*",
            (string)$require->getRequiredWpVersion()
        );

        $requiredPlugins = $require->getRequiredPlugins();
        $requiredPlugin = $requiredPlugins["myplugin/myplugin.php"];

        $this->assertEquals(
            "1.0.0",
            (string)$requiredPlugin
        );
    }

    public function testThatNotSpesifingPluginVersionInPluginDataWorksAsExpected() {
        // Create a plugin that does not spesify a version number
        $plugin = WPRequireTestUtils::createMockPlugin(array(), array());

        $this->assertEquals("0.0.0-alpha0", (string)$plugin->getVersion());
    }

}
