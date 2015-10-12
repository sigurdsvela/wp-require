<?php
namespace WPRequireTest\lib;

use WPRequireTest\util\WPRequireTestUtils;
use WPRequire\WPRequire;
use WPRequire\lib\Version;

class WPAddonTest extends \WP_UnitTestCase {


    public function testUnsuportedWpVersion() {
        $plugin = WPRequireTestUtils::createMockPlugin(array(
                "wordpress" => "10.0.0"
            ));

        $unmetRequirements = $plugin->getUnmetRequirements();

        $this->assertTrue(isset($unmetRequirements["wp"]), "WP Index not set");

        $this->assertTrue($unmetRequirements["wp"][0] instanceof Version, "WP was not of type version");
        $this->assertTrue($unmetRequirements["wp"][1] instanceof Version, "WP was not of type version");

        $this->assertEquals(
            array("10.0.0", (string)WPRequire::getWpVersion()),
            array((string)$unmetRequirements["wp"][0], (string)$unmetRequirements["wp"][1])
        );

        $this->assertEquals(1, count($unmetRequirements));
    }

    public function testUnsuportedPhpVersion() {
        $plugin = WPRequireTestUtils::createMockPlugin(array(
                "php" => "10.0.0"
            ));

        $unmetRequirements = $plugin->getUnmetRequirements();

        $this->assertTrue(isset($unmetRequirements["php"]));

        $this->assertTrue($unmetRequirements["php"][0] instanceof Version);
        $this->assertTrue($unmetRequirements["php"][1] instanceof Version);

        $this->assertEquals(
            array("10.0.0", (string)WPRequire::getPhpVersion()),
            array((string)$unmetRequirements["php"][0], (string)$unmetRequirements["php"][1])
        );

        $this->assertEquals(1, count($unmetRequirements));
    }

    public function getUnsuportedPluginVersion() {
        $requiredPlugin = WPRequireTestUtils::createMockPlugin(array(
            ),
            array(
                'Version' => '1.0.0'
            )
        );

        $plugin = WPRequireTestUtils::createMockPlugin(array(
                'plugins' => array(
                    $requiredPlugin->getPluginFile => '2.0.0'
                )
            ),
            array(
            )
        );

        $unmetRequirements = $plugin->getUnmetRequirements();

        $this->assertEquals(
            array("2.0.0", "1.0.0"),
            array(
                (string)$unmetRequirements["plugins"][$requiredPlugin->getPluginFile()][0],
                (string)$unmetRequirements["plugins"][$requiredPlugin->getPluginFile()][1]
            )
        );

        $this->assertEquals(1, count($unmetRequirements));
        $this->assertEquals(1, count($unmetRequirements["plugins"]));
    }

    public function getUnsuppliedPlugin() {
        $plugin = WPRequireTestUtils::createMockPlugin(array(
                "plugin" => array(
                    "na/na.php" => "1.0.0"
                )
            ));

        $res = $plugin->getUnmetRequirements();

        $this->assertEquals(array("1.0.0"), $res['plugins']['na/na.php']);
    }

    public function testGetRequiredPhpVersion() {
        $plugin = WPRequireTestUtils::createMockPlugin(array("php" => "1.0.0"));

        $this->assertEquals("1.0.0", $plugin->getRequiredPhpVersion());
    }

    public function getRequiredWpVersion() {
        $plugin = WPRequireTestUtils::createMockPlugin(array("wp" => "1.0.0"));

        $this->assertEquals("1.0.0", $plugin->getRequiredWpVersion());
    }
}