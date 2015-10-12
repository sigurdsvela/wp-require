<?php
namespace WPRequire;

use WPRequire\lib\WPPlugin;
use WPRequire\lib\WPTheme;
use WPRequire\lib\Version;

use std\parser\Json;

/**
* Main class
*/
class WPRequire {
    private static $adminNotices = [];

    /**
     * Entry point
     */
    public static function main() {
        self::managePluginsBaseOnRequirement();

        /* Print admin notices on the admin_notices hook */
        add_action('admin_notices', function() {
            foreach (self::$adminNotices as $adminNotice) {
                ?>
                <div class="<?php echo $adminNotice["class"] ?>">
                    <p><?php echo $adminNotice["text"] ?></p>
                </div>
                <?php
            }
        });
    }

    /**
     * The path to the root folder of this plugin
     *
     * @return string The path to the root folder of this plugin
     */
    public static function ABSPATH() {
        return WP_REQUIRE_ABSPATH;
    }

    /**
     * Get the path to the plugins directory
     *
     * @return string Path to the plugins directory
     */
    public static function PLUGINS_DIR() {
        return ABSPATH . '/wp-content/plugins';
    }

    /**
     * Get the path to the themes directory
     *
     * @return string Path to the themes directory
     */
    public static function THEMES_DIR() {
        return ABSPATH . '/wp-content/themes';
    }

    /**
     * Deactivates plugins base on requirements. And adds admin notices
     * if the a plugin is deactivated.
     */
    private static function managePluginsBaseOnRequirement() {
        /* Holds the plugins that must be deactivated */
        /* In plugin-base-file=>[$pluginObject, missing-part] pairs */
        $toDeactivate = self::getUnsuportedPlugins();

        foreach ($toDeactivate as $pluginFile => $reasons) {
            self::deactivatePlugin($pluginFile);
            
            $resonsString = self::buildReadableResonsString($pluginFile, $reasons);

            self::addAdminNotice(
                "<strong>Deactivated $pluginFile: </strong>" . $resonsString,
                "error"
            );
        }
    }

    /**
     * The "getUnsuportedPlugins" and "getUnsuportedTheme"
     * functions returns the reson why the plugin or theme was
     * unsuported. This take one of those, and builds a readable
     * string.
     *
     * @return string The readable string
     */
    private static function buildReadableResonsString($addonName, $reasons) {
        $string = "$addonName was unsuported because ";
        if (isset($reasons['php'])) {
            $string .= " $addonName requires version {$reasons['php'][0]} of php, " .
            " {$reasons['php'][1]} was supplied.";
        }

        if (isset($reasons['wp'])) {
            $string .= "<br>And $addonName requires version {$reasons['wp'][0]} of WordPress, " .
            " {$reasons['wp'][1]} was supplied.";
        }

        if (!isset($reasons['plugins'])) return $string;

        foreach ($reasons['plugins'] as $pluginName => $value) {
            $string .= "<br>And $addonName requires version <strong>{$value[0]}</strong> of the plugin <strong>$pluginName</strong>, ";

            if (isset($value[1])) {
                $string .= " {$value[1]} was supplied.";
            } else {
                $string .= " none was supplied.";
            }
        }

        return $string;
    }

    /**
     * Get active plugins that does not have there requirements met.
     * returns an array with plugin-base-name=>["this"=>[required-version, supplied-version]]
     * Supplied version in this array will be "null" if there was non supplied. eg. If it was a plugin
     * that wa entierly missing.
     *
     * @return array plugin-name=>(outdatedOrMissing=>(required, supplied))[]
     */
    private static function getUnsuportedPlugins() {
        $activePlugins = self::getAllActivePlugins();

        $unsuported = [];
        foreach ($activePlugins as $plugin) {
            $pluginFile = $plugin->getPluginFile();
            $wpRequireFile = $plugin->getWpRequire();

            // If no wp-require file exists, assume it has all it needs
            if ($wpRequireFile === null) continue;

            // Init the $unsuported array for this plugin
            $unsuported[$pluginFile] = array();
            
            $requiredPhpVersion = $wpRequireFile->getRequiredPhpVersion();
            $requiredWpVersion = $wpRequireFile->getRequiredWpVersion();
            $requiredPlugins = $wpRequireFile->getRequiredPlugins();

            $phpComp = $requiredPhpVersion->isCompatibleWith(self::getPhpVersion());
            if (!$phpComp) {
                $unsuported[$pluginFile]["php"] = array($requiredPhpVersion, self::getPhpVersion());
            }

            $wpComp = $requiredWpVersion->isCompatibleWith(self::getWpVersion());
            if (!$wpComp) {
                $unsuported[$pluginFile]["wp"] = array($requiredWpVersion, self::getWpVersion());
            }

            $unsuported[$pluginFile]['plugins'] = array();

            foreach($requiredPlugins as $requiredPluginFile => $requiredPluginVersion) {
                if (!self::isPluginActive($requiredPluginFile)) {
                    if (!isset($unsuported[$pluginFile]['plugins']))
                        $unsuported[$pluginFile]['plugins'] = array();
                    
                    $unsuported[$pluginFile]['plugins'][$requiredPluginFile] = array($requiredPluginVersion, null);
                } else {
                    $pluginData = get_plugin_data(WPRequire::PLUGINS_DIR() . "/" . $requiredPluginFile);

                    $requiredVersion = new Version($requiredPluginVersion);
                    $suppliedVersion = new Version($pluginData["Version"]);

                    if (!$requiredVersion->isCompatibleWith($suppliedVersion)) {
                        $unsuported[$pluginFile]['plugins'][$requiredPluginFile] = array($requiredPluginVersion, new Version($pluginData["Version"]));
                    }
                }
            }


            // If this plugins plugin requirments was uphelp
            if (count($unsuported[$pluginFile]['plugins']) === 0)
                unset($unsuported[$pluginFile]['plugins']);
            
            // If no reasons for why this plugin is unsuported can be found
            // Remove it from the array
            if (count($unsuported[$pluginFile]) === 0)
                unset($unsuported[$pluginFile]);
        }

        return $unsuported;
    }

    public static function getPhpVersion() {
        return new Version(PHP_VERSION);
    }

    public static function getWpVersion() {
        global $wp_version;
        return new Version($wp_version);
    }

    private static function addAdminNotice($text, $type = "update") {
        $notice = [];
        if ($type === "update") {
            $notice["class"] = "updated";
        } elseif ($type === "error") {
            $notice["class"] = "error";
        } else {
            throw new \InvalidArgumentException("WPRequire::addAdminNotice expects \"update\" or \"error\" as the second argument");
        }
        $notice["text"] = $text;
        array_push(self::$adminNotices, $notice);
    }

    /**
     * Get the version number
     */
    public static function version() {

    }

    private static function activatePlugin($basefile) {
        $pluginFiles = get_option('active_plugins');
        array_push($pluginFiles, $basefile);
        update_option('active_plugins', $pluginFiles);
    }

    /**
     * Deactivate a plugin.
     * Removes a plugin from the active_plugins option.
     * If there are more than one instance of the plugin in
     * active_plugins, all will be removed.
     *
     * @param string $baseFile The base file for the plugin to deactivate
     *
     * @return void
     */
    private static function deactivatePlugin($basefile) {
        $pluginFiles = get_option('active_plugins');

        $keys = array_keys($pluginFiles, $basefile);
        foreach($keys as $key) {
            unset($pluginFiles[$key]);
        }

        update_option('active_plugins', $pluginFiles);
        $pluginFiles = get_option('active_plugins');
    }

    /**
     * Checks if a plugin exists in the active_plugins option.
     *
     * @param string $baseFile The basefile for the plugin
     *
     * @return bool True if it does, false if it dosent.
     */
    private static function isPluginActive($baseFile) {
        return array_search($baseFile, get_option('active_plugins')) !== false;
    }

    private static function getAllActivePlugins() {
        $plugins = [];
        $pluginFiles = get_option('active_plugins');
        foreach ($pluginFiles as $k => $pluginFile) {
            array_push($plugins, new WPPlugin($pluginFile));
        }
        return $plugins;
    }

    /**
     * Get the current theme
     * 
     * @return WPTheme The current theme
     */
    public static function getActiveTheme() {
        return new WPTheme(get_template_directory());
    }
}
