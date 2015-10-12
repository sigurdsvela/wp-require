<?php
namespace WPRequireTest\lib;

use WPRequire\lib\Version;

class VersionTest extends \WP_UnitTestCase {

    /* # Test the __toString() method # */
    function testToString() {
        $version = new Version("1.2.3");
        $this->assertEquals("1.2.3", (string)$version);
    }

    function testToStringWithRC() {
        $version = new Version("1.2.3-rc1");
        $this->assertEquals("1.2.3-rc1", (string)$version);
    }

    function testToStringWithBeta() {
        $version = new Version("1.2.3-b1");
        $this->assertEquals("1.2.3-beta1", (string)$version);
    }

    function testToStringWithAlpha() {
        $version = new Version("1.2.3-a1");
        $this->assertEquals("1.2.3-alpha1", (string)$version);
    }

    /* # Test The Compare method. $version1 is allways less # */

    function testCompareMajorWithoutSpecials() {
        $version1 = new Version("1.3.4");
        $version2 = new Version("2.2.1");
        $this->assertEquals(-1, $version1->compare($version2));
        $this->assertEquals(1, $version2->compare($version1));
    }

    function testCompareMinorWithoutSpecials() {
        $version1 = new Version("1.1.2");
        $version2 = new Version("1.2.0");
        $this->assertEquals(-1, $version1->compare($version2));
        $this->assertEquals(1, $version2->compare($version1));
    }

    function testComparePatchWithoutSpecials() {
        $version1 = new Version("1.1.1");
        $version2 = new Version("1.1.2");
        $this->assertEquals(-1, $version1->compare($version2));
        $this->assertEquals(1, $version2->compare($version1));
    }

    function testCompareWildcardMajorWithoutSpecials() {
        $version1 = new Version("*.1.1");
        $version2 = new Version("*.1.2");
        $this->assertEquals(-1, $version1->compare($version2));
        $this->assertEquals(1, $version2->compare($version1));
    }

    function testCompareWildcardMinorWithoutSpecials() {
        $version1 = new Version("1.*.1");
        $version2 = new Version("2.*.1");
        $this->assertEquals(-1, $version1->compare($version2));
        $this->assertEquals(1, $version2->compare($version1));
    }

    function testCompareWildcardPatchWithoutSpecials() {
        $version1 = new Version("1.1.*");
        $version2 = new Version("1.2.*");
        $this->assertEquals(-1, $version1->compare($version2));
        $this->assertEquals(1, $version2->compare($version1));
    }

    function testThatRCVersionIsLess() {
        $version1 = new Version("1.0.0-rc");
        $version2 = new Version("1.0.0");
        $this->assertEquals(-1, $version1->compare($version2));
        $this->assertEquals(1, $version2->compare($version1));
    }

    function testRCCompare() {
        $version1 = new Version("1.0.0-rc1");
        $version2 = new Version("1.0.0-rc2");
        $this->assertEquals(-1, $version1->compare($version2));
        $this->assertEquals(1, $version2->compare($version1));
    }

    function testThatBetaVersionIsLess() {
        $version1 = new Version("1.0.0-beta");
        $version2 = new Version("1.0.0");
        $this->assertEquals(-1, $version1->compare($version2));
        $this->assertEquals(1, $version2->compare($version1));
    }

    function testBetaCompare() {
        $version1 = new Version("1.0.0-beta1");
        $version2 = new Version("1.0.0-beta2");
        $this->assertEquals(-1, $version1->compare($version2));
        $this->assertEquals(1, $version2->compare($version1));
    }

    function testThatAlphaVersionIsLess() {
        $version1 = new Version("1.0.0-alpha");
        $version2 = new Version("1.0.0");
        $this->assertEquals(-1, $version1->compare($version2));
        $this->assertEquals(1, $version2->compare($version1));
    }

    function testAlphaCompare() {
        $version1 = new Version("1.0.0-alpha1");
        $version2 = new Version("1.0.0-alpha2");
        $this->assertEquals(-1, $version1->compare($version2));
        $this->assertEquals(1, $version2->compare($version1));
    }

    /* # Test the "isCompatibleWith" method # */
    function testAllWildcardInRequiredIsAllwaysCompatable() {
        $required = new Version("*.*.*");
        $supplied = new Version("1.1.1");
        $this->assertTrue($required->isCompatibleWith($supplied));
    }

    function testIsCompatableWithMinorWildcard() {
        $required = new Version("1.*");
        $supplied = new Version("1.1.1");
        $this->assertTrue($required->isCompatibleWith($supplied));   
    }

    function testIsCompatableWithPatchWildcard() {
        $required = new Version("1.1.*");
        $supplied = new Version("1.1.1");
        $this->assertTrue($required->isCompatibleWith($supplied));   
    }

    function testThatIfSuppliedIsSmallerItReturnsFalse() {
        $required = new Version("2.2.0");
        $supplied = new Version("2.1.0");
        $this->assertFalse($required->isCompatibleWith($supplied));
    }

    function testThatIfSuppliedIsAnotherMajortItRetrurnsFalse() {
        $required = new Version("1.0.0");
        $supplied = new Version("2.0.0");
        $this->assertFalse($required->isCompatibleWith($supplied));
    }

    function testThatIfSuppliedIsABiggerMinorItReturnsTrue() {
        $required = new Version("1.0.0");
        $supplied = new Version("1.1.0");
        $this->assertTrue($required->isCompatibleWith($supplied));
    }

    function testThatIfSuppliedIsABiggerPatchItReturnsTrue() {
        $required = new Version("1.0.0");
        $supplied = new Version("1.0.1");
        $this->assertTrue($required->isCompatibleWith($supplied));
    }

    function testThatIfSuppliedIsAnRCItReturnsTrue() {
        $required = new Version("1.0.0");
        $supplied = new Version("1.0.0-rc");
        $this->assertFalse($required->isCompatibleWith($supplied));
    }

    function testThatIfSuppliedIsAnBetaItReturnsTrue() {
        $required = new Version("1.0.0");
        $supplied = new Version("1.0.0-beta");
        $this->assertFalse($required->isCompatibleWith($supplied));
    }

    function testThatIfSuppliedIsAnAlphaItReturnsTrue() {
        $required = new Version("1.0.0");
        $supplied = new Version("1.0.0-alpha");
        $this->assertFalse($required->isCompatibleWith($supplied));
    }

    /* # Test the parsing # */

    function testThatParsingWorksAsExpected() {
        $version = new Version("1.2.3");
        $this->assertEquals(1, $version->getMajor(), "Failed to parse MAJOR version");
        $this->assertEquals(2, $version->getMinor(), "Failed to parse MINOR version");
        $this->assertEquals(3, $version->getPatch(), "Failed to parse PATCH version");

        $this->assertFalse($version->isRC());
        $this->assertFalse($version->isBeta());
        $this->assertFalse($version->isAlpha());
        $this->assertEquals(null, $version->getRC());
        $this->assertEquals(null, $version->getBeta());
        $this->assertEquals(null, $version->getAlpha());
    }

    function testThatParsingWorksAsExpectedWithRc() {
        $version = new Version("1.2.3-rc4");
        $this->assertEquals(1, $version->getMajor(), "Failed to parse MAJOR version");
        $this->assertEquals(2, $version->getMinor(), "Failed to parse MINOR version");
        $this->assertEquals(3, $version->getPatch(), "Failed to parse PATCH version");

        $this->assertTrue($version->isRC());
        $this->assertFalse($version->isBeta());
        $this->assertFalse($version->isAlpha());

        $this->assertEquals(4, $version->getRC());
        $this->assertEquals(null, $version->getBeta());
        $this->assertEquals(null, $version->getAlpha());
    }

    function testThatParsingWorksAsExpectedWithBeta() {
        $version = new Version("1.2.3-beta4");
        $this->assertEquals(1, $version->getMajor(), "Failed to parse MAJOR version");
        $this->assertEquals(2, $version->getMinor(), "Failed to parse MINOR version");
        $this->assertEquals(3, $version->getPatch(), "Failed to parse PATCH version");

        $this->assertFalse($version->isRC());
        $this->assertTrue($version->isBeta());
        $this->assertFalse($version->isAlpha());

        $this->assertEquals(null, $version->getRC());
        $this->assertEquals(4, $version->getBeta());
        $this->assertEquals(null, $version->getAlpha());
    }

    function testThatParsingWorksAsExpectedWithB() {
        $version = new Version("1.2.3-b4");
        $this->assertEquals(1, $version->getMajor(), "Failed to parse MAJOR version");
        $this->assertEquals(2, $version->getMinor(), "Failed to parse MINOR version");
        $this->assertEquals(3, $version->getPatch(), "Failed to parse PATCH version");

        $this->assertFalse($version->isRC());
        $this->assertTrue($version->isBeta());
        $this->assertFalse($version->isAlpha());

        $this->assertEquals(null, $version->getRC());
        $this->assertEquals(4, $version->getBeta());
        $this->assertEquals(null, $version->getAlpha());
    }

    function testThatParsingWorksAsExpectedWithAlpha() {
        $version = new Version("1.2.3-alpha4");
        $this->assertEquals(1, $version->getMajor(), "Failed to parse MAJOR version");
        $this->assertEquals(2, $version->getMinor(), "Failed to parse MINOR version");
        $this->assertEquals(3, $version->getPatch(), "Failed to parse PATCH version");

        $this->assertFalse($version->isRC());
        $this->assertFalse($version->isBeta());
        $this->assertTrue($version->isAlpha());

        $this->assertEquals(null, $version->getRC());
        $this->assertEquals(null, $version->getBeta());
        $this->assertEquals(4, $version->getAlpha());
    }

    function testThatParsingWorksAsExpectedWithA() {
        $version = new Version("1.2.3-a4");
        $this->assertEquals(1, $version->getMajor(), "Failed to parse MAJOR version");
        $this->assertEquals(2, $version->getMinor(), "Failed to parse MINOR version");
        $this->assertEquals(3, $version->getPatch(), "Failed to parse PATCH version");

        $this->assertFalse($version->isRC());
        $this->assertFalse($version->isBeta());
        $this->assertTrue($version->isAlpha());

        $this->assertEquals(null, $version->getRC());
        $this->assertEquals(null, $version->getBeta());
        $this->assertEquals(4, $version->getAlpha());
    }
}