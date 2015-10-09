<?php
namespace WPRequireTest;

use std\util\Str;
use std\io\File;
use std\io\FileWriter;
use std\parser\Json;

use WPRequire\WPRequire;
use WPRequire\lib\WPPlugin;
use WPRequire\lib\Version;

use WPRequireTest\util\WPRequireTestUtils;

class WPRequireTest extends \WP_UnitTestCase {
    /* @var File[] contains file to detele on tearDown */
    private $toDelete = [];

    public function testActivatePlugin() {
        $WPRequire = new WPRequire();
        $mockPlugin = WPRequireTestUtils::createMockPlugin(array());
        $mockPluginFile = $mockPlugin->getPluginFile();

        // Activate the mock plugin
        WPRequireTestUtils::invokeMethod($WPRequire, "activatePlugin", [$mockPluginFile]);

        $plugins = WPRequireTestUtils::invokeMethod(
            $WPRequire,
            "getAllActivePlugins",
            [$mockPluginFile]
        );

        $wasActivated = false;
        foreach($plugins as $plugin) {
            if ($plugin->getPluginFile() === $mockPluginFile) {
                $wasActivated = true;
            }
        }

        $this->assertTrue($wasActivated);
    }

    public function testGetUnsuportedPluginsOutdatedPhp() {
        $WPRequire = new WPRequire();
        $mockPhpVersionRequire = new Version("10.0.0");

        // Create the mock plugin
        $mockPlugin = WPRequireTestUtils::createMockPlugin(array(
            "php" => (string)$mockPhpVersionRequire
        ));

        $mockPluginFile = $mockPlugin->getPluginFile();

        // Activate the mock plugin
        WPRequireTestUtils::invokeMethod($WPRequire, "activatePlugin", [$mockPluginFile]);

        // Get unsuported plugins
        $unsuported = WPRequireTestUtils::invokeMethod($WPRequire, "getUnsuportedPlugins");

        // Check if our mock plugin is considered usuported(as it should)
        $this->assertTrue(isset($unsuported[$mockPluginFile]));
        
        // Test that the PHP reason is marked as expected
        $this->assertEquals(
            $unsuported[$mockPluginFile]["php"],
            array(
                $mockPlugin->getWpRequire()->getRequiredPhpVersion(),
                WPRequire::getPhpVersion()
            )
        );

        // Test that there is only one reason for this to be unsuported
        $this->assertEquals(count($unsuported[$mockPluginFile]), 1);
    }

    public function testGetUnsuportedPluginsOutdatedWp() {
        $WPRequire = new WPRequire();
        $mockWpVersionRequire = new Version("10.0.0");

        // Create the mock plugin
        $mockPlugin = WPRequireTestUtils::createMockPlugin(array(
            "wordpress" => (string)$mockWpVersionRequire
        ));

        $mockPluginFile = $mockPlugin->getPluginFile();

        // Activate the mock plugin
        WPRequireTestUtils::invokeMethod($WPRequire, "activatePlugin", [$mockPluginFile]);

        // Get unsuported plugins
        $unsuported = WPRequireTestUtils::invokeMethod($WPRequire, "getUnsuportedPlugins");

        // Check if our mock plugin is considered usuported(as it should)
        $this->assertTrue(isset($unsuported[$mockPluginFile]));
        
        // Test that the PHP reason is marked as expected
        $this->assertEquals(
            $unsuported[$mockPluginFile]["wp"],
            array(
                $mockPlugin->getWpRequire()->getRequiredWpVersion(),
                WPRequire::getWpVersion()
            )
        );

        // Test that there is only one reason for this to be unsuported
        $this->assertEquals(count($unsuported[$mockPluginFile]), 1);
    }

    public function testGetUnsuportedPluginsMissingPlugin() {
        $WPRequire = new WPRequire();

        $mockRequiredPlugins = array(
            "na/na.php" => "5.3.1"
        );

        // Create the mock plugin
        $mockPlugin = WPRequireTestUtils::createMockPlugin(array(
            "plugins" => $mockRequiredPlugins
        ));

        $mockPluginFile = $mockPlugin->getPluginFile();

        // Activate the mock plugin
        WPRequireTestUtils::invokeMethod($WPRequire, "activatePlugin", [$mockPluginFile]);

        // Get unsuported plugins
        $unsuported = WPRequireTestUtils::invokeMethod($WPRequire, "getUnsuportedPlugins");

        // Check if our mock plugin is considered usuported(as it should)
        $this->assertTrue(isset($unsuported[$mockPluginFile]));
        
        // Test that the PHP reason is marked as expected
        $this->assertEquals(
            $unsuported[$mockPluginFile]['plugins']["na/na.php"],
            array(
                new Version("5.3.1"),
                false
            )
        );

        // Test that there is only one reason for this to be unsuported
        $this->assertEquals(count($unsuported[$mockPluginFile]), 1);
    }

    public function testGetUnsuportedPluginsOutdatedPlugin() {
        $WPRequire = new WPRequire();

        $requiredPlugin = WPRequireTestUtils::createMockPlugin(array());
        $requiredPluginFile = $requiredPlugin->getPluginFile();

        // Create the mock plugin
        $mockPlugin = WPRequireTestUtils::createMockPlugin(array(
            "plugins" => array(
                $requiredPluginFile => "2.0.0"
            )
        ));

        $mockPluginFile = $mockPlugin->getPluginFile();

        // Activate the mock plugin
        WPRequireTestUtils::invokeMethod($WPRequire, "activatePlugin", [$mockPluginFile]);
        WPRequireTestUtils::invokeMethod($WPRequire, "activatePlugin", [$requiredPluginFile]);

        // Get unsuported plugins
        $unsuported = WPRequireTestUtils::invokeMethod($WPRequire, "getUnsuportedPlugins");

        // Check if our mock plugin is considered usuported(as it should)
        $this->assertTrue(isset($unsuported[$mockPluginFile]));
        
        // Test that the PHP reason is marked as expected
        $this->assertEquals(
            array(
                new Version("2.0.0"),
                new Version("1.0.0")
            ),
            $unsuported[$mockPluginFile]['plugins'][$requiredPluginFile]
        );

        // Test that there is only one reason for this to be unsuported
        $this->assertEquals(count($unsuported[$mockPluginFile]['plugins']), 1);
    }

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

    function tearDown() {
        foreach($this->toDelete as $file) {
            $file->delete();
        }
    }
}

