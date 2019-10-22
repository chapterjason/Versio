<?php
/**
 * This file is part of the Versio package.
 *
 * (c) Jason Schilling <jason.schilling@sourecode.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * File that was distributed with this source code.
 */

namespace Versio\Version;

use Versio\Exception\InvalidVersionException;
use Versio\Utils;

class Version
{

    public static $expression = '(?P<major>0|[1-9]\d*)\.(?P<minor>0|[1-9]\d*)\.(?P<patch>0|[1-9]\d*)(?:-(?P<prerelease>(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*)(?:\.(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*))*))?(?:\+(?P<buildmetadata>[0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?';

    /**
     * @var int $major
     */
    private $major;
    /**
     * @var int $minor
     */
    private $minor;
    /**
     * @var int $patch
     */
    private $patch;
    /**
     * @var string[] $prerelease
     */
    private $prerelease;
    /**
     * @var string[] $build
     */
    private $build;

    /**
     * Version constructor.
     * @param int $major
     * @param int $minor
     * @param int $patch
     * @param string[] $prerelease
     * @param string[] $build
     */
    public function __construct(int $major, int $minor, int $patch, array $prerelease = [], array $build = [])
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->prerelease = Utils::lengthFilter($prerelease);
        $this->build = Utils::lengthFilter($build);
    }

    public static function clone(Version $version): Version
    {
        return new Version(
            $version->getMajor(),
            $version->getMinor(),
            $version->getPatch(),
            $version->getPrerelease(),
            $version->getBuild()
        );
    }

    /**
     * @return int
     */
    public function getMajor(): int
    {
        return $this->major;
    }

    /**
     * @param int $major
     * @return Version
     */
    public function setMajor(int $major): Version
    {
        $this->major = $major;

        return $this;
    }

    /**
     * @return int
     */
    public function getMinor(): int
    {
        return $this->minor;
    }

    /**
     * @param int $minor
     * @return Version
     */
    public function setMinor(int $minor): Version
    {
        $this->minor = $minor;

        return $this;
    }

    /**
     * @return int
     */
    public function getPatch(): int
    {
        return $this->patch;
    }

    /**
     * @param int $patch
     * @return Version
     */
    public function setPatch(int $patch): Version
    {
        $this->patch = $patch;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getPrerelease(): array
    {
        return $this->prerelease;
    }

    /**
     * @param string[] $prerelease
     * @return Version
     */
    public function setPrerelease($prerelease): Version
    {
        $this->prerelease = $prerelease;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getBuild(): array
    {
        return $this->build;
    }

    /**
     * @param string[] $build
     * @return Version
     */
    public function setBuild(array $build): Version
    {
        $this->build = $build;

        return $this;
    }

    /**
     * @param string $version
     * @return Version
     */
    public static function parse(string $version): Version
    {
        $expression = '/^' . self::$expression . '$/';

        if (preg_match($expression, $version, $matches)) {
            return new Version(
                Utils::strictParseInt($matches['major']),
                Utils::strictParseInt($matches['minor']),
                Utils::strictParseInt($matches['patch']),
                isset($matches['prerelease']) ? explode('.', $matches['prerelease']) : [],
                isset($matches['buildmetadata']) ? explode('.', $matches['buildmetadata']) : []
            );
        }

        throw new InvalidVersionException($version);
    }

    /**
     * @return string[][]
     */
    public function getExtra(): array
    {
        return Utils::convertToExtra($this->prerelease);
    }

    /**
     * @param string[][] $extra
     * @return $this
     */
    public function setExtra(array $extra): Version
    {
        $this->prerelease = Utils::convertToPrerelease($extra);

        return $this;
    }

    public function __toString()
    {
        return $this->format();
    }

    public function format(): string
    {
        $version = $this->major . '.' . $this->minor . '.' . $this->patch;

        if (count($this->prerelease) > 0) {
            $version .= '-' . implode('.', $this->prerelease);
        }

        if (count($this->build) > 0) {
            $version .= '+' . implode('.', $this->build);
        }

        return $version;
    }


}