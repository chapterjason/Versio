<?php
/**
 * This file is part of the Versio package.
 *
 * (c) Jason Schilling <jason.schilling@sourecode.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * File that was distributed with this source code.
 */

namespace Versio\Strategy;

use ErrorException;
use ReflectionObject;
use Versio\Version\Version;

abstract class AbstractStrategy implements StrategyInterface
{

    /**
     * @var array $options
     */
    protected $options;

    public function setOptions(array $options = []): void
    {
        $this->options = $options;
    }

    abstract public function update(Version $version): void;

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     * @throws ErrorException
     */
    protected function getOption(string $key, $default = null)
    {
        $value = $this->options[$key] ?? null;

        if (null === $value) {
            if (null === $default) {
                $reflection = new ReflectionObject($this);
                throw new ErrorException(
                    'Missing option key "' . $key . '" in strategy "' . $reflection->getName() . '".'
                );
            }

            return $default;
        }

        return $value;
    }

}