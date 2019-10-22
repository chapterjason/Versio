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


use function implode;
use function str_replace;

class VersionReplacer
{

    /**
     * @var Version $version
     */
    private $version;

    /**
     * VersionReplacer constructor.
     * @param Version $version
     */
    public function __construct(Version $version)
    {
        $this->version = $version;
    }

    public function replace(string $text)
    {
        return str_replace(
            [
                '{{VERSION_MAJOR}}',
                '{{VERSION_MINOR}}',
                '{{VERSION_PATCH}}',
                '{{VERSION_PRERELEASE}}',
                '{{VERSION_BUILD}}',
                '{{VERSION}}',
            ],
            [
                $this->version->getMajor(),
                $this->version->getMinor(),
                $this->version->getPatch(),
                implode('.', $this->version->getPrerelease()),
                implode('.', $this->version->getBuild()),
                $this->version->format(),
            ],
            $text
        );
    }

}