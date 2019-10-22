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
use Versio\Version\VersioFile;
use Versio\Version\Version;

class StrategyManager
{

    /**
     * @var StrategyResolver $resolver
     */
    protected $resolver;

    /**
     * StrategyManager constructor.
     * @param StrategyResolver $resolver
     */
    public function __construct(StrategyResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @param VersioFile $versioFile
     * @param Version $version
     * @throws ErrorException
     */
    public function update(VersioFile $versioFile, Version $version): void
    {
        $strategies = $this->resolver->resolve($versioFile);

        foreach ($strategies as $strategy) {
            $strategy->update($version);
        }
    }

}