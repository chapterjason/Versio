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

    public abstract function update(Version $version): void;

}