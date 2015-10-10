<?php
namespace WPRequire\lib;

use WPRequire\WPRequire;
use WPRequire\WPRequireFile;

class WPPlugin extends WPAddon{
    /** @var string The basefile for the plugin */
    private $pluginFile;

    /** @var string|null Null if this plugin is not a folder */
    private $pluginFolder;

    /** @var Version|null Null if not on admin */
    private $version = null;

    /**
     * The unique name of the plugin
     */
    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
        $this->pluginFolder = dirname($this->pluginFile);

        //If pluginFolder is empty string, the plugin is not a folder
        //But just a single file
        if ($this->pluginFolder === "") {
            $this->pluginFolder = null;
        }

        if (function_exists('get_plugin_data')) {
            $pluginData = get_plugin_data(WPRequire::PLUGINS_DIR() . "/" . $this->pluginFile);
            $this->version = new Version($pluginData["Version"]);
        }

        parent::__construct();
    }

    /**
     * Return the absolut path to the root of the plugin, or null
     * if this plugin is a single file.
     *
     * @return string|null
     */
    protected function getPath() {
        if ($this->getPluginFolder() === null) {
            return null;
        } else {
            return WPRequire::PLUGINS_DIR() . "/" . $this->getPluginFolder();
        }
    }

    /**
     * Get the version of this plugin
     * This only works on admin. Becaus, for some stupid fucking reason
     * wordpresses "get_plugin_data" only workd on admin.
     * 
     * @return Version|null Null if not on admin
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Get the plugin folder. Returns null if this plugin
     * is not a folder.
     *
     * @return string|null
     */
    public function getPluginFolder() {
        return $this->pluginFolder;
    }

    /**
     * Get the plugin basefile
     *
     * @return string
     */
    public function getPluginFile() {
        return $this->pluginFile;
    }

    /**
     * Activate the plugin
     *
     * @return void
     */
    public function activate() {
        activate_plugin($this->pluginFile);
    }

    /**
     * Deactivate the plugin
     *
     * @return void
     */
    public function deactivate() {
        deactivale_plugins($this->pluginFile);
    }
}
