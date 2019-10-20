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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Versio\Version\VersioFileManager;

class StrategyFactory
{

    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * StrategyFactory constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $type
     * @param array $options
     * @return StrategyInterface|null
     * @throws ErrorException
     */
    public function createStrategy(string $type, array $options = []): ?StrategyInterface
    {
        $strategy = null;

        if ($type === 'versio') {
            $strategy = new VersioStrategy($this->container->get(VersioFileManager::class));
        } else if ($type === 'composer') {
            $strategy = new ComposerStrategy();
        }

        if (null !== $strategy) {
            $strategy->setOptions($options);
            $strategy->validateOptions();
        }

        return $strategy;
    }

}