<?php
namespace WPRequire;

use std\parser\Json;
use WPRequire\lib\Version;

/*
 * Contains methods to get information from a spesific WPRequireFile
 */
class WPRequireFile {
    /** @var Version The required WordPress version */
    private $requiredWpVersion;

    /** @var Version The required PHP version */
    private $requiredPhpVersion;

    /** @var Array plugin-file=>Version pairs */
    private $requiredPlugins;

    /*
     * Takes a path, and compiles the file to get the information inside
     *
     * @param string path The path to the wp-require file
     */
    public function __construct($path) {
        $json = new Json();
        $json->parseString(file_get_contents($path));
        if ($json->offsetExists("wordpress"))
            $this->requiredWpVersion = new Version(
                $json->offsetGet("wordpress")
            );
        else
            $this->requiredWpVersion = new Version("*.*.*");

        if ($json->offsetExists("php"))
            $this->requiredPhpVersion = new Version(
                $json->offsetGet("php")
            );
        else
            $this->requiredPhpVersion = new Version("*.*.*");

        $this->requiredPlugins = [];
        if ($json->offsetExists("plugins")) {
            $requiredPlugins = $json->offsetGet("plugins");
            foreach($requiredPlugins as $pluginFileName => $version) {
                $this->requiredPlugins[$pluginFileName] = new Version($version);
            }
        }
    }

    /**
     * Get the required WordPress version
     *
     * Will return a Version object with all wildcards
     * if no required WordPress version was supplied.
     *
     * @return Version The required WordPress version
     */
    public function getRequiredWpVersion() {
        return $this->requiredWpVersion;
    }

    /**
     * Get the required PHP version
     *
     * Will return a Version object with all wildcards
     * if no required PHP version was supplied.
     *
     * @return Version The required PHP version
     */
    public function getRequirePhpVersion() {
        return $this->requiredPhpVersion;
    }

    /**
     * Returns a hash map that contains plugin-slug=>WPRequire\lib\Version pairs
     *
     * @return {string=>Version}
     */
    public function getRequiredPlugins() {
        return $this->requiredPlugins;
    }

}