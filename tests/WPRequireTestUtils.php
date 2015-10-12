<?php
namespace WPRequireTest\util;

use std\io\FileWriter;
use std\io\FileReader;
use std\io\File;
use std\util\Str;

use WPRequire\WPRequire;
use WPRequire\lib\WPTheme;

use std\parser\Json;

use WPRequire\lib\WPPlugin;

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

    private static function createDataHeader(array $data) {
        $dataString = "/*";
        foreach ($data as $key => $value) {
            $dataString .= " *$key: $value\n";
        }
        $dataString .= " */";
        return $dataString;
    }

    /**
     * Create a mock plugin that is deleted
     * when the tests are done running 
     * with a wp-require file
     * with the contents of $require.
     *
     * @param array $requires The desired contents of the wp-require file.
     *
     * @return WPPlugin The plugin
     */
    public static function createMockPlugin($requires, array $pluginData = array('Version' => '1.0.0')) {
        $basePluginDir = WPRequire::PLUGINS_DIR();

        // Create a random name
        $pluginName = Str::random(20);

        $pluginData['Plugin Name'] =  $pluginName;
        $pluginDataString = self::createDataHeader($pluginData);

        // Create the plugin directory
        $pluginDir = new File($basePluginDir . "/$pluginName");
        $pluginDir->createDir();
        
        // Create the base plugin file
        $pluginFile = new File($pluginDir->getPath() . "/$pluginName.php");
        $pluginFile->createFile();

        // Write plugin name and version to the plugin header
        $pluginFileWriter = new FileWriter($pluginFile);
        $pluginFileWriter->open();
        $pluginFileWriter->write($pluginDataString);
        $pluginFileWriter->close();
        
        // Create the wp-require.json file
        $wpRequireFile = new File($pluginDir->getPath() . "/wp-require.json");
        $wpRequireFile->createFile();
        $wpRequireFileWriter = new FileWriter($wpRequireFile);
        
        // Write the array as json to the wp-require.json file
        $wpRequireFileWriter->open();
        $wpRequireFileWriter->write((string)(new Json($requires)));
        $wpRequireFileWriter->close();

        // Request files to be deleted on tearDown
        $pluginFile->deleteOnExit();
        $wpRequireFile->deleteOnExit();
        $pluginDir->deleteOnExit();

        $plugin = new WPPlugin("$pluginName/$pluginName.php");
        return $plugin;
    }

    /**
     * Create a mock theme that is deleted
     * when the tests are done running 
     * with a wp-require file
     * with the contents of $require.
     *
     * @param array $requires The desired contents of the wp-require file.
     *
     * @return WPTheme The theme
     */
    public static function createMockTheme($requires, array $themeData = array('Version' => '1.0.0')) {
        $baseThemesDir = WPRequire::THEMES_DIR();

        // Create a random name
        $themeName = Str::random(20);

        $themeData['Theme Name'] = $themeName;
        $themeDataHeader = self::createDataHeader($themeData);

        // Create the plugin directory
        $themeDir = new File($baseThemesDir . "/$themeName");
        $themeDir->createDir();
        
        // Create the base plugin file
        $styleCss = new File($themeDir->getPath() . "/style.css");
        $styleCss->createFile();

        // Write plugin name and version to the plugin header
        $styleCssWriter = new FileWriter($styleCss);
        $styleCssWriter->open();
        $styleCssWriter->write($themeDataHeader);
        $styleCssWriter->close();
        
        // Create the wp-require.json file
        $wpRequireFile = new File($themeDir->getPath() . "/wp-require.json");
        $wpRequireFile->createFile();
        $wpRequireFileWriter = new FileWriter($wpRequireFile);
        
        // Write the array as json to the wp-require.json file
        $wpRequireFileWriter->open();
        $wpRequireFileWriter->write((string)(new Json($requires)));
        $wpRequireFileWriter->close();

        // Request files to be deleted on tearDown
        $styleCss->deleteOnExit();
        $wpRequireFile->deleteOnExit();
        $themeDir->deleteOnExit();

        $theme = new WPTheme("$themeName");
        return $theme;
    }
}