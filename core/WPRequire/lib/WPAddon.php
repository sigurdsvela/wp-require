<?php
namespace WPRequire\lib;

use WPRequire\WPRequireFile;
use WPRequire\lib\Version;
use WPRequire\WPRequire;

/**
 * The superclass for WPTheme and WPPlugin
 */
abstract class WPAddon {
    private $wpRequireFile;

    protected final static function DEFAULT_VERSION() {
        return new Version("0.0.0-a0");
    }

    /**
     * Sets up the basics for all addons
     */
    protected function __construct() {
        if (file_exists($this->getWpRequireFilePath())) {
            $this->wpRequireFile = new WPRequireFile($this->getWpRequireFilePath());
        } else {
            $this->wpRequireFile = null;
        }
    }

    /**
     * Get the name for this addon.
     * Returns an empty string if none is spesified
     * 
     * @return string
     */
    public abstract function getName();

    /**
     * If this is a not single file addon
     * , returns the root directory of this addon.
     * if this is a single file addon(single file plugin),
     * return null.
     *
     * @return string|null The path
     */
    protected abstract function getPath();

    /**
     * Get the version of this addon.
     * If no version is spesified for this addon, 0.0.0-a0 is assumed.
     *
     * @return Version The version of this addon
     */
    public abstract function getVersion();

    /**
     * Get the wp-require file path.
     * This function will return NULL if the wp-require file
     * is not present. Use this function to check for a wp-require files
     * presence
     *
     * @return string|null The path to the wp-require file. Or null if not
     * applicable
     */
    public final function getWpRequireFilePath() {
        // If the wp-require file is not present
        if (!file_exists($this->getPath() . "/wp-require.json")) return null;

        return $this->getPath() . "/wp-require.json";
    }

    /**
     * Return an instance of WPRequireFile, or null, if
     * this addon has no WPRequire file
     *
     * @return WPRequireFile|null
     */
    public final function getWpRequire() {
        return $this->wpRequireFile;
    }

    /**
     * Get the required PHP version for this plugin
     * 
     * If none is spesified 0.0.0-a0 is assumed
     * 
     * @return Version
     */
    public final function getRequiredPhpVersion() {
        if ($this->getWpRequire() === null) {
            return self::DEFAULT_VERSION();
        } else {
            return $this->getWpRequire()->getRequiredPhpVersion();
        }
    }

    /**
     * Get the required WordPress version for this plugin
     * 
     * If none is spesified 0.0.0-a0 is assumed
     * 
     * @return Version
     */
    public final function getRequiredWpVersion() {
        if ($this->getWpRequire() === null) {
            return self::DEFAULT_VERSION();
        } else {
            return $this->getWpRequire()->getRequiredPhpVersion();
        }
    }

    /**
     * Checks if this addon's requirements are met
     * If the are, this will return an empty array,
     * if not, this function will return an array of the
     * things that are missing
     * with this structure
     * 
     * <pre>
     * array(
     *     'php' => array('required version', 'supplied version'),
     *     'wp'  => array('require version', 'supplied version'),
     *     'plugins' => array(
     *         'plugin/basefile.php' => array('required version', 'supplied version')
     *     )
     * )
     * </pre>
     * 
     * For everything that is supplied and correct, the field will not
     * be set.
     * Forexample, if the PHP version if compatible, the PHP
     * field will not be set.
     * 
     * If a plugin is not supplied at all, the array will be only
     * one index, containing only the required version.
     */
    public final function getUnmetRequirements() {
        $return = array();

        $phpVersion = WPRequire::getPhpVersion();
        $requiredPhpVersion = $this->getWpRequire()->getRequiredPhpVersion();


        $wpVersion = WPRequire::getWpVersion();
        $requiredWpVersion = $this->getWpRequire()->getRequiredWpVersion();

        if (!$requiredPhpVersion->isCompatibleWith($phpVersion)) {
            $return["php"] = array($requiredPhpVersion, $phpVersion);
        }

        if (!$requiredWpVersion->isCompatibleWith($wpVersion)) {
            $return["wp"] = array($requiredWpVersion, $wpVersion);
        }

        $requiredPlugins = $this->getWpRequire()->getRequiredPlugins();

        $return['plugins'] = array();
        foreach($requiredPlugins as $requiredPluginFile => $requiredPluginVersion) {
            // If the plugin is not active
            if (!WPRequire::isPluginActive($requiredPluginFile)) {                
                $return['plugins'][$requiredPluginFile] = array($requiredPluginVersion, null);
            } else {
                // If the plugin is active, lets check the versions
                $pluginData = get_plugin_data(WPRequire::PLUGINS_DIR() . "/" . $requiredPluginFile);

                $requiredVersion = new Version($requiredPluginVersion);
                $suppliedVersion = new Version($pluginData["Version"]);

                if (!$requiredVersion->isCompatibleWith($suppliedVersion)) {
                    $return['plugins'][$requiredPluginFile] = array(
                        $requiredPluginVersion,
                        new Version($pluginData["Version"])
                    );
                }
            }
        }

        // If there where no unsupported plugins, unset the 'plugins' index
        if (count($return['plugins']) === 0) {
            unset($return['plugins']);
        }

        return $return;
    }
}
