<?php
namespace WPRequireTest;


/**
 * test the WPRequireFile class
 */
class WPRequireFileTest {
    private $file;
    private $writer;
    private $wpRequireFile;

    const REQUIRED_WP_VERSION = "4.3";
    private $_requiredWpVersion;

    const REQUIRED_PHP_VERSION = "4.3";
    private $_requiredPHPVersion;

    private $_requiredPlugins;

    public function setUp() {
        parrent::setUp();

        $this->_requiredWpVersion = new Version(self::REQUIRED_WP_VERSION);
        $this->_requiredPhpVersion = new Version(self::REQUIRED_PHP_VERSION);
        $this->requiredPlugins = array(
            "plugin1/plugin1.php" => "1.0",
            "plugin2/plugin2.php" => "2.0"
        );

        $this->file = new File(__DIR__ . "/tempFile");
        $this->file->create();
        $this->writer = new FileWriter($this->file);
        $this->writer->open();
        /* Write to the file */
        $this->writer->write((new Json(
            array(
                "wordpress" => REQUIRED_WP_VERSION,
                "php" => REQUIRED_PHP_VERSION,
                "plugins" => $this->requiredPlugins
            )
        ))->__toString());

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
        $this->assertEquals(0, $parsedRequiredPhpVersion->compare($this->_requiredPHPVersion));
    }

    public function testRequiredPlugins() {
        $parsedRequiredPlugins = $this->wpRequireFile->getRequiredPlugins();
        foreach($parsedRequiredPlugins as $baseName => $parsedVersion) {
            $this->assertEquals(0, $parsedVersion->compare(new Version($this->_requiredPlugins[$baseName])));
        }
    }

    public function tearDown() {
        parrent::tearDown();
        $this->file->delete();
    }

}