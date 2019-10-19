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

use Versio\Utils;

class VersionManager
{

    public function setDev(Version $version, bool $dev): void
    {
        if ($this->isDev($version)) {
            if (!$dev) {
                $version->setPrerelease([]);
            }
        } else if ($this->isKindOfDev($version)) {
            if (!$dev) {
                $prerelease = implode('.', $version->getPrerelease());
                $updatedPrerelease = preg_replace('/-DEV$/', '', $prerelease);
                $version->setPrerelease(explode('.', $updatedPrerelease));
            }
        } else if ($dev) {
            if ($this->isKindOfType($version)) {
                $extra = $version->getExtra();
                $extra[] = ['DEV'];
                $version->setExtra($extra);
            } else {
                $version->setPrerelease(['DEV']);
            }
        }
    }

    public function isDev(Version $version): bool
    {
        $extra = $version->getExtra();

        if (count($extra) !== 1) {
            return false;
        }

        if (count($extra[0]) !== 1) {
            return false;
        }

        return $extra[0][0] === 'DEV';
    }

    public function isKindOfDev(Version $version): bool
    {
        $extra = $version->getExtra();

        if (count($extra) === 0) {
            return false;
        }

        $lastBlock = $extra[count($extra) - 1];
        $lastIdentifier = end($lastBlock);

        return $lastIdentifier === 'DEV';
    }

    public function isKindOfType(Version $version): bool
    {
        $extra = $version->getExtra();

        if (count($extra) <= 0) {
            return false;
        }

        if (count($extra[0]) !== 2) {
            return false;
        }

        if (is_nan(Utils::strictParseInt($extra[0][1]))) {
            return false;
        }

        return true;
    }

    public function getType(Version $version): ?string
    {
        if (!$this->isKindOfType($version)) {
            return null;
        }

        $extra = $version->getExtra();

        return $extra[0][0];
    }

    public function unsetType(Version $version): void
    {
        if ($this->isKindOfType($version)) {
            if ($this->isKindOfDev($version)) {
                $version->setPrerelease(['DEV']);
            } else {
                $version->setPrerelease([]);
            }
        }
    }

    public function setType(Version $version, string $type): void
    {
        if ($this->isKindOfDev($version)) {
            $version->setExtra([[$type, '1'], ['DEV']]);
        } else {
            $version->setExtra([[$type, '1']]);
        }
    }

    public function isTypeDev(Version $version, string $type): bool
    {
        $extra = $version->getExtra();

        if (count($extra) !== 2) {
            return false;
        }

        if (count($extra[0]) !== 2) {
            return false;
        }

        if ($extra[0][0] !== strtoupper($type)) {
            return false;
        }

        if (is_nan(Utils::strictParseInt($extra[0][1]))) {
            return false;
        }

        if ($extra[1][0] !== 'DEV') {
            return false;
        }

        return true;
    }

    public function isType(Version $version, string $type): bool
    {
        $extra = $version->getExtra();

        if (count($extra) !== 1) {
            return false;
        }

        if (count($extra[0]) !== 2) {
            return false;
        }

        if ($extra[0][0] !== strtoupper($type)) {
            return false;
        }

        if (is_nan(Utils::strictParseInt($extra[0][1]))) {
            return false;
        }

        return true;
    }

    public function incrementMajor(Version $version): void
    {
        $version->setMajor($version->getMajor() + 1);
        $version->setMinor(0);
        $version->setPatch(0);
    }

    public function incrementMinor(Version $version): void
    {
        $version->setMinor($version->getMinor() + 1);
        $version->setPatch(0);
    }

    public function incrementPatch(Version $version): void
    {
        $version->setPatch($version->getPatch() + 1);
    }

    public function incrementType(Version $version): void
    {
        if ($this->isKindOfType($version)) {
            $extra = $version->getExtra();

            $value = Utils::strictParseInt($extra[0][1]);

            if (is_nan($value)) {
                return;
            }

            $extra[0][1] = $value + 1;

            $version->setExtra($extra);
        }
    }

}