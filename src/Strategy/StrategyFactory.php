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
use Symfony\Component\Config\Definition\Processor;
use Versio\Configuration\ComposerStrategyConfiguration;
use Versio\Configuration\ExpressionStrategyConfiguration;
use Versio\Configuration\LineStrategyConfiguration;
use Versio\Configuration\NpmStrategyConfiguration;
use Versio\Configuration\VersioStrategyConfiguration;
use Versio\Version\VersioFileManager;

class StrategyFactory
{

    /**
     * @var VersioFileManager $versioFileManager
     */
    protected $versioFileManager;

    /**
     * StrategyFactory constructor.
     * @param VersioFileManager $versioFileManager
     */
    public function __construct(VersioFileManager $versioFileManager)
    {
        $this->versioFileManager = $versioFileManager;
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
        $configuration = null;

        if ($type === 'versio') {
            $configuration = new VersioStrategyConfiguration();
            $strategy = new VersioStrategy($this->versioFileManager);
        } else if ($type === 'composer') {
            $configuration = new ComposerStrategyConfiguration();
            $strategy = new ComposerStrategy();
        } else if ($type === 'expression') {
            $configuration = new ExpressionStrategyConfiguration();
            $strategy = new ExpressionStrategy();
        } else if ($type === 'line') {
            $configuration = new LineStrategyConfiguration();
            $strategy = new LineStrategy();
        } else if ($type === 'npm') {
            $configuration = new NpmStrategyConfiguration();
            $strategy = new NpmStrategy();
        } else {
            throw new ErrorException('Unknown strategy "' . $type . '".');
        }

        if (null === $configuration) {
            throw new ErrorException('Missing strategy configuration.');
        }

        $processor = new Processor();
        $processedConfiguration = $processor->processConfiguration($configuration, [$options]);

        if (null !== $strategy) {
            $strategy->setOptions($processedConfiguration);
        }

        return $strategy;
    }

}