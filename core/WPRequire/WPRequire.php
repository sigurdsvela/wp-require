<?php
namespace WPRequire;

use WPRequire\lib\WPPlugin;

/**
* Main class
*/
class WPRequire
{

    /**
     * Entry point
     */
    public static function main() {

    }

    /**
     * Get the version number
     */
    public static function version() {

    }

    public static function getAllActivePlugins() {
        $plugins = [];
        $pluginFiles = get_option('active_plugins');
        foreach ($pluginFiles as $k => $pluginFile) {
            array_push($plugins, new WPPlugin($pluginFile));
        }
        return $plugins;
    }
}
