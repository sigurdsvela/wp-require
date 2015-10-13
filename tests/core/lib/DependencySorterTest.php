<?php
namespace WPRequireTest\lib;

use WPRequireTest\util\WPRequireTestUtils;

use WPRequire\lib\DependencySorter;

class DependencySorterTest extends \WP_UnitTestCase{
    

    public function testBasicStructure() {
        /*
         * plugin requires(->) plugin
         *
         * 0 -> 1,2
         * 1 -> 2
         * 2 -> 3
         * 3 -> nothing
         * 4 -> 0,2
         *
         * Correct Order
         * 3
         * 2
         * 1
         * 0
         * 4
         * 
         */
        

        $plugin3 = WPRequireTestUtils::createMockPlugin(
            array('plugins' => array()),
            array('Plugin Name' => 'Plugin3')
        );

        $plugin2 = WPRequireTestUtils::createMockPlugin(
            array('plugins' => array(
                $plugin3->getPluginFile() => "1.0.0"
            )),
            array('Plugin Name' => 'Plugin2')
        );

        $plugin1 = WPRequireTestUtils::createMockPlugin(
            array('plugins' => array(
                $plugin2->getPluginFile() => "1.0.0"
            )),
            array('Plugin Name' => 'Plugin1')
        );

        $plugin0 = WPRequireTestUtils::createMockPlugin(
            array('plugins' => array(
                $plugin1->getPluginFile() => "1.0.0",
                $plugin2->getPluginFile() => "1.0.0"
            )),
            array('Plugin Name' => 'Plugin0')
        );

        $plugin4 = WPRequireTestUtils::createMockPlugin(
            array('plugins' => array(
                $plugin0->getPluginFile() => "1.0.0",
                $plugin2->getPluginFile() => "1.0.0"
            )),
            array('Plugin Name' => 'Plugin4')
        );

        $pluginArray = array($plugin0, $plugin1, $plugin2, $plugin3, $plugin4);

        $dependencySorter = new DependencySorter($pluginArray);

        $this->assertEquals(
            array($plugin3, $plugin2, $plugin1, $plugin0, $plugin4),
            $dependencySorter->getDependencySortedArray()
        );
    }

}
