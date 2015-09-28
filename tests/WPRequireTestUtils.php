<?php
namespace WPRequireTest\util;

class WPRequireTestUtils {

    /**
     * Used to invoke private methods for unit testing
     * 
     * @param Object $object The class object to get the method from
     * @param string $methodName The name of the method to invoke
     * @param array $parameters An array of parameters to pass to the method
     * @throws any Whatever the real method would have thrown
     * @return any Whatever the real method would return
     */
    public static function invokeMethod($object, $methodName, $parameters = []) {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Get a private field from an object
     *
     * @param Object $object The object to get the field from
     * @param string $variableName The variable name
     * @return any The value of the variable
     */
    public static function getField($object, $variableName) {
        $variableName = (string)$variableName; //Check that its string castable
        $reflection = new \ReflectionClass(get_class($object));
        $variable = $reflection->getProperty($variableName);
        $variable->setAccessible(true);

        return $variable->getValue();
    }

}