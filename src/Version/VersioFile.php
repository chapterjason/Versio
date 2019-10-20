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

class VersioFile
{

    /**
     * @var array
     */
    private $configuration;

    /**
     * VersioFile constructor.
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getVersion(): Version
    {
        return Version::parse($this->configuration['version']);
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function getStrategies(): array
    {
        $strategies = [
            [
                'type' => 'versio',
            ],
        ];

        $configuredStrategies = $this->configuration['strategies'];

        if (null !== $configuredStrategies) {
            $strategies = array_merge($strategies, $configuredStrategies);
        }

        return $strategies;
    }

    public function setVersion(Version $version): VersioFile
    {
        $this->configuration['version'] = $version->format();

        return $this;
    }

}