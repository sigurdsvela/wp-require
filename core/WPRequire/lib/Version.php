<?php
namespace WPRequire\lib;

use std\util\Str;

class Version {
    private $major;
    private $minor;
    private $patch;
    private $rc = null;
    private $beta = null;
    private $alpha = null;

    /*
     * Take a version string and parses it.
     * supports n.n.n[-ln]
     *
     * -ln part snads for somthing like. -rc3, or -beta1/-b1.
     * It can also ommit the numer. 1.0.0-beta for instance
     *
     * It also support wildcard at any place.
     */
    public function __construct($stringVersion) {
        /* Parse the version into segments */
        $segments = explode(".", $stringVersion);
        $segCount = count($segments);

        if ($segCount < 2 || $segCount > 3)
            throw new \InvalidArgumentException($this->stdInvalidArgumentMessage());

        //When patch is ommited. It is interperated as a wildcard.
        if ($segCount == 2)
            $segments[2] = "*";

        $spesVers = explode("-", $segments[2]);
        $segments[2] = $spesVers[0];

        if (!isset($spesVers[1]))
            $spesVers[1] = "";
        
        $spesVers = $spesVers[1];
        /* end */

        $this->major = $segments[0];
        $this->minor = $segments[1];
        $this->patch = $segments[2];

        if (Str::startsWith($spesVers, "rc")) {
            $this->rc = (int)Str::subString($spesVers, 2);
        } else if (Str::startsWith($spesVers, "alpha")) {
            $this->alpha = (int)Str::subString($spesVers, 5);
        } else if (Str::startsWith($spesVers, "a")) {
            $this->alpha = (int)Str::subString($spesVers, 1);
        } else if (Str::startsWith($spesVers, "beta")) {
            $this->beta = (int)Str::subString($spesVers, 4);
        } else if (Str::startsWith($spesVers, "b")) {
            $this->beta = (int)Str::subString($spesVers, 1);
        } else if ($spesVers === "") { //This means that -beta, -alpha and -rc was not supplied
        } else {
            throw new \InvalidArgumentException($this->stdInvalidArgumentMessage());
        }
    }

    private function stdInvalidArgumentMessage() {
        return "Version constructur expects at least 2 digits, maximum 3, seperated by a period(.). " +
                "Digits can be replace with *, but only *. The supported postfixes are -a, -alpha, -b, " +
                "-beta, -rc. All of these can have an arbitraty amount of digits after.";
    }

    /**
     * Get the MAJOR
     * {MAJOR}.0.0-rc1
     *
     * @return int|string int or the string "*"
     */
    public function getMajor() {
        return $this->major;
    }

    /**
     * Get the MINOR
     * 0.{MINOR}.0-rc1
     *
     * @return int|string int or the string "*"
     */
    public function getMinor() {
        return $this->minor;
    }

    /**
     * Get the PATCH
     * 0.0.{PATCH}-rc1
     *
     * @return int|string int or the string "*"
     */
    public function getPatch() {
        return $this->patch;
    }

    /**
     * Check if this is an RC version
     *
     * @return bool
     */
    public function isRC() {
        return ($this->rc !== null);
    }

    /**
     * Check if this is a Beta version
     *
     * @return bool
     */
    public function isBeta() {
        return ($this->beta !== null);
    }

    /**
     * Check if this is an Alpha version
     *
     * @return bool
     */
    public function isAlpha() {
        return ($this->alpha !== null);
    }

    /**
     * Get the RC version
     * MINOR.MAJOR.PATCH-rc{RC-VERSION}
     *
     * @return int|null null if it is not an RC version. The number otherwhise. Zero if none supplied.
     */
    public function getRC() {
        return $this->rc;
    }

    /**
     * Get the BETA version
     * MINOR.MAJOR.PATCH-beta{BETA-VERSION}
     * MINOR.MAJOR.PATCH-b{BETA-VERSION}
     *
     * @return int|null null if it is not an Beta version. The number otherwhise. Zero if none supplied.
     */
    public function getBeta() {
        return $this->beta;
    }

    /**
     * Get the ALPHA version
     * MINOR.MAJOR.PATCH-alpha{ALPHA-VERSION}
     * MINOR.MAJOR.PATCH-a{ALPHA-VERSION}
     *
     * @return int|null null if it is not an Alpha version. The number otherwhise. Zero if none supplied.
     */
    public function getAlpha() {
        return $this->alpha;
    }

    public function compare(Version $version) {
        if ($this->getMajor() !== "*" && $version->getMajor() !== "*") {
            if ($this->getMajor() > $version->getMajor()) return -1;
            if ($version->getMajor() > $this->getMajor()) return 1;
        }

        if ($this->getMinor() !== "*" && $version->getMinor() !== "*") {
            if ($this->getMinor() > $version->getMinor()) return -1;
            if ($version->getMinor() > $this->getMinor()) return 1;
        }

        if ($this->getPatch() !== "*" && $version->getPatch() !== "*") {
            if ($this->getPatch() > $version->getPatch()) return -1;
            if ($version->getPatch() > $this->getPatch()) return 1;
        }

        if (!$this->isRC() && $version->isRC()) return -1;
        if ($this->isRC()) {
            if (!$version->isRC()) return 1;
            if ($this->getRC() === "*" || $version->getRC() === "*") return 0;
            if ($this->getRC() > $version->getRC()) return -1;
            if ($version->getRC() > $this->getRC()) return 1;
            return 0;
        }

        if (!$this->isBeta() && $version->isBeta()) return -1;
        if ($this->isBeta()) {
            if (!$version->isBeta()) return 1;
            if ($this->getBeta() === "*" || $version->getBeta() === "*") return 0;
            if ($this->getBeta() > $version->getBeta()) return -1;
            if ($version->getBeta() > $this->getBeta()) return 1;
            return 0;
        }

        if (!$this->isAlpha() && $version->isAlpha()) return -1;
        if ($this->isAlpha()) {
            if (!$version->isAlpha()) return 1;
            if ($this->getAlpha() === "*" || $version->getAlpha() === "*") return 0;
            if ($this->getAlpha() > $version->getAlpha()) return -1;
            if ($version->getAlpha() > $this->getAlpha()) return 1;
            return 0;
        }

        return 0;
    }

    /**
     * 
     * Checks if this version is compatible with the version passed in
     * Think of the THIS version to be the "required" version, and the passed version
     * to be the supplied version.
     *
     * For example, if this version is 1.2.0, and the one passed in is 1.3.2
     * this function will return true.
     *
     * This function will return false if the version passed in is greater than this version
     * <code>
     *   $version = new Version("1.3.2");
     *   $version->isCompatibleWith("1.3.1"); //false
     * </code>
     *
     * or, if the version passed in is another "major" version.
     * for example
     *
     * <code>
     *   $version = new Version("1.0.0");
     *   $version->isCompatibleWith("2.0.0"); //false
     * </code>
     */
    public function isCompatibleWith(self $version) {
        //If the version passed in is smaller
        if ($this->compare($version) === -1) return false;

        //If $version is another major version
        if ($this->getMajor() !== $version->getMajor()) return false;

        return true;
    }

    /**
     * Return as a string
     * the special(eg, -beta, -rc etc.) will be ommited if it was not spesified.
     * PATCH will be * if it was not spesified
     */
    public function __toString() {
        $string = $this->getMajor() . "." . $this->getMinor() . "." . $this->getPatch();
        if ($this->isRC())
            $string .= "-" . $this->getRC();
        
        if ($this->isBeta())
            $string .= "-" . $this->getBeta();

        if ($this->isAlpha())
            $string .= "-" . $this->getAlpha();

        return $string;
    }
}
