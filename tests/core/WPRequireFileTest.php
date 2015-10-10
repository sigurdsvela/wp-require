<?php
namespace WPRequireTest;

use WPRequire\lib\Version;
use WPRequire\WPRequireFile;
use std\io\File;
use std\io\FileWriter;
use std\io\FileReader;
use std\parser\Json;


/**
 * test the WPRequireFile class
 */
class WPRequireFileTest extends \WP_UnitTestCase {
    private $file;
    private $writer;
    private $reader;
    private $wpRequireFile;

    const REQUIRED_WP_VERSION = "4.3";
    private $_requiredWpVersion;

    const REQUIRED_PHP_VERSION = "4.3";
    private $_requiredPhpVersion;

    private $_requiredPlugins;

    public function setUp() {
        parent::setUp();

        $this->_requiredWpVersion = new Version(self::REQUIRED_WP_VERSION);
        $this->_requiredPhpVersion = new Version(self::REQUIRED_PHP_VERSION);
        $this->_requiredPlugins = array(
            "plugin1/plugin1.php" => "1.0",
            "plugin2/plugin2.php" => "2.0"
        );

        $this->file = new File(ABSPATH . "/tempfile.json");
        $this->file->createFile();
        $this->writer = new FileWriter($this->file);
        $this->writer->open();
        /* Write to the file */

        $this->writer->write((new Json(
            array(
                "wordpress" => self::REQUIRED_WP_VERSION,
                "php" => self::REQUIRED_PHP_VERSION,
                "plugins" => $this->_requiredPlugins
            )
        ))->__toString());

        $this->reader = new FileReader($this->file);

        $this->wpRequireFile = new WPRequireFile($this->file->getPath());
    }

    public function testRequiredWPVersion() {
        // Check that the file parsed correctly
        // This compares the Version object created to
        // The version object parsed by the WPRequireFile class
        $parsedRequiredWpVersion = $this->wpRequireFile->getRequiredWpVersion();
        $this->assertEquals(0, $parsedRequiredWpVersion->compare($this->_requiredWpVersion));
    }

    public function testRequiredPhpVersion() {
        // Check that the file parsed correctly
        // This compares the Version object created to
        // The version object parsed by the WPRequireFile class
        $parsedRequiredPhpVersion = $this->wpRequireFile->getRequiredPhpVersion();
        $this->assertEquals(0, $parsedRequiredPhpVersion->compare($this->_requiredPhpVersion));
    }

    public function testRequiredPlugins() {
        $parsedRequiredPlugins = $this->wpRequireFile->getRequiredPlugins();
        foreach($parsedRequiredPlugins as $baseName => $parsedVersion) {
            $this->assertEquals(0, $parsedVersion->compare(new Version($this->_requiredPlugins[$baseName])));
        }
        $this->assertFalse(empty($parsedRequiredPlugins));
    }

    public function tearDown() {
        parent::tearDown();
        $this->file->delete();
    }

}