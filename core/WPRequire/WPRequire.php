<?php
namespace WPRequire;

use WPRequire\lib\WPPlugin;
use WPRequire\lib\Version;

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
     * Deactivates plugins base on requirements. And adds admin notices
     * if the a plugin is deactivated.
     */
    private static function managePluginsBaseOnRequirement() {
        self::activatePlugin("akismet/akismet.php");

        /* Holds the plugins that must be deactivated */
        /* In plugin-base-file=>[$pluginObject, missing-part] pairs */
        $toDeactivate = self::getUnsuportedPlugins();

        
    }

    /**
     * Get active plugins that does not have there requirements met.
     * returns an array with plugin-base-name=>["this"=>[required-version, supplied-version]]
     * Supplied version in this array will be "null" if there was non supplied. eg. If it was a plugin
     * that wa entierly missing.
     */
    private static function getUnsuportedPlugins() {
        $activePlugins = self::getAllActivePlugins();

        $unsuported = [];
        foreach ($activePlugins as $plugin) {
            $pluginFile = $plugin->getPluginFile();
            $unsuported[$pluginFile] = array();


            $wpRequireFile = $plugin->getWpRequire();
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

            foreach($requiredPlugins as $requiredPluginFile => $requiredPluginVersion) {
                if (!is_plugin_active($requiredPluginFile)) {
                    if (!isset($unsuported[$pluginFile]))
                        $unsuported[$pluginFile] = array();
                    $unsuported[$pluginFile][$requiredPluginFile] = array($requiredPluginVersion, null);
                } else {
                    $pluginData = get_plugin_data(WPRequire::ABSPATH() . "/../" . $requiredPluginFile);

                    if (!isset($pluginData["Version"])) {
                        throw new \Exception("Version not set");
                        //TODO Set some flag to tell that it is being tried to require a plugin without versioning
                        continue;
                    }

                    $requiredVersion = new Version($requiredPluginVersion);
                    $suppliedVersion = new Version($pluginData["Version"]);

                    if (!$requiredVersion->isCompatibleWith($suppliedVersion)) {
                        $unsuported[$pluginFile][$requiredPluginFile] = array($requiredPluginVersion, new Version($pluginData["Version"]));
                    }
                }
            }
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

    public static function getWordPressVersion() {

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

    private static function deactivatePlugin($basefile) {
        $pluginFiles = get_option('active_plugins');
        $key = array_search($basefile, $plugin);
        unset($pluginFiles[$key]);
        update_option('active_plugins', $pluginFiles);
    }

    private static function getAllActivePlugins() {
        $plugins = [];
        $pluginFiles = get_option('active_plugins');
        foreach ($pluginFiles as $k => $pluginFile) {
            array_push($plugins, new WPPlugin($pluginFile));
        }
        return $plugins;
    }
}
