<?php
namespace WPRequire\lib;

class WPPlugin {
    private $pluginFile;

    /**
     * The unique name of the plugin
     */
    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
    }

    public function getVersion() {

    }

    public function getPluginFile() {
        return $this->pluginFile;
    }

    /**
     * Return an instance of WPRequireFile, or null, if
     * said plugin had no WPRequire file
     *
     * @return WPRequireFile|null
     */
    public function getWpRequire() {

    }

}