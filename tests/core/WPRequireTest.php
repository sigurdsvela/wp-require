<?php
namespace WPRequireTest;

use WPRequire\WPRequire;
use WPRequire\lib\WPPlugin;

class WPRequireTest extends \WP_UnitTestCase {

    function testGetAllActivePlugins() {
        activate_plugin("akismet/akismet.php");

        /* To invoke the PRIVATE static method "getAllActivePlugins" */
        $WPRequire = new WPRequire();
        $activePlugins = $this->invokeMethod($WPRequire, "getAllActivePlugins");

        $this->assertEquals(
            "akismet/akismet.php",
            $activePlugins[0]->getPluginFile()
        );

        $this->assertTrue($activePlugins[0] instanceof WPPlugin);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}

