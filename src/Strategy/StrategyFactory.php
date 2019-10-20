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

        if ($type === 'versio') {
            $strategy = new VersioStrategy($this->versioFileManager);
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