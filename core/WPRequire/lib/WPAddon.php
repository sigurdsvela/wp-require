<?php
namespace WPRequire\lib;

use WPRequire\WPRequireFile;

/**
 * The superclass for WPTheme and WPPlugin
 */
abstract class WPAddon {
    private $wpRequireFile;

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
}
