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

interface StrategyInterface
{

    public function setOptions(array $options = []): void;

    public function update(Version $version): void;

}