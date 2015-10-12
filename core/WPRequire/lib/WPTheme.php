<?php
namespace WPRequire\lib;

use WPRequire\WPRequire;

class WPTheme extends WPAddon {
    /** @var string[string] All the data from the style.css file */
    private $themeData;

    /** @var Version the version of the theme, as staded in style.css */
    private $version;

    /** @var The path to the root of this theme */
    private $path;

    /**
     * Construct a new WPTheme object
     *
     * @param string $path The path to the root folder of the theme
     */
    public function __construct($path) {
        $this->path = WPRequire::THEMES_DIR() . "/" . $path;

        $themeObject = wp_get_theme(basename($this->path));

        // Extract the theme data into an assosiativ array
        $this->themeData = array(
            "Name"        => $themeObject->get("Name"),
            "ThemeURI"    => $themeObject->get("ThemeURI"),
            "Description" => $themeObject->get("Description"),
            "Author"      => $themeObject->get("Author"),
            "AuthorURI"   => $themeObject->get("AuthorURI"),
            "Version"     => $themeObject->get("Version"),
            "Template"    => $themeObject->get("Template"),
            "Status"      => $themeObject->get("Status"),
            "Tags"        => $themeObject->get("Tags"),
            "TextDomain"  => $themeObject->get("TextDomain"),
            "DomainPath"  => $themeObject->get("DomainPath"),
        );

        if (isset($this->themeData['version'])) {
            $this->version = new Version($this->themeData['version']);
        } else {
            // Null if none is spesified
            $this->version = new Version("0.0.0-a0");
        }

        parent::__construct();
    }

    /**
     * Get the name for this theme
     * 
     * @return string
     */
    public function getName() {
        return $this->themeData["Name"];
    }

    public function getPath() {
        return $this->path;
    }

    /**
     * Get the version of this theme.
     * 0.0 is assumed if the version is not spesified in style.css
     *
     * @return Version
     */
    public function getVersion() {
        return $this->version;
    }

}
