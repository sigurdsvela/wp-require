<?php
namespace WPRequire;

/*
 * Contains methods to get information from a spesific WPRequireFile
 */
class WPRequireFile {

    /*
     * Takes a path, and compiles the file to get the information inside
     *
     * @param string path The path to the wp-require file
     */
    public function __construct($path) {

    }

    public function getRequiredWpVersion() {

    }

    public function getRequirePhpVersion() {

    }

    /**
     * Returns a hash map that contains plugin-slug=>WPRequire\lib\Version pairs
     *
     * @return {string=>Version}
     */
    public function getRequiredPlugins() {
        
    }

}