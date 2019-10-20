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


use Versio\Version\VersioFile;

class StrategyResolver
{

    /**
     * @var StrategyFactory $factory
     */
    protected $factory;

    /**
     * StrategyResolver constructor.
     * @param StrategyFactory $factory
     */
    public function __construct(StrategyFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param VersioFile $versioFile
     * @return StrategyInterface[]
     */
    public function resolve(VersioFile $versioFile): array
    {
        $strategies = [];
        $strategiesConfiguration = $versioFile->getStrategies();

        foreach ($strategiesConfiguration as $strategyConfiguration) {
            $strategies[] = $this->factory->createStrategy(
                $strategyConfiguration['type'],
                $strategyConfiguration['options'] ?? []
            );
        }

        return $strategies;
    }

}