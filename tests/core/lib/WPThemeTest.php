<?php
namespace WPRequireTest\lib;

use WPRequire\lib\WPTheme;
use WPRequire\WPRequireFile;

use WPRequireTest\util\WPRequireTestUtils;

class WPThemeTest extends \WP_UnitTestCase {

    public function testGetWpRequireFile() {
        $theme = WPRequireTestUtils::createMockTheme(
            array(
                "php" => "1.0.0",
                "wordpress" => "4.3",
                "plugins" => array(
                    "myplugin/myplugin.php" => "1.0.0"
                )
            )
        );
        $require = $theme->getWpRequire();

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

    public static function getVersionForThemeWithoutSpesifiedVersion() {
        $theme = WPRequireTestUtils::createMockTheme(array(),array());
        $this->assertEquals("0.0.0-alpha0", $theme->getVersion());
    }

    public function testGetName() {
        $theme = WPRequireTestUtils::createMockTheme(
            array(),
            array("Theme Name" => "MyName")
        );

        $this->assertEquals("MyName", $theme->getName());
    }

}
