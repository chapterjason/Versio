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
use ReflectionException;
use Symfony\Component\Process\Process;
use Versio\Version\Version;

class ComposerStrategy extends AbstractStrategy
{

    /**
     * @param Version $version
     * @throws ErrorException
     * @throws ReflectionException
     */
    public function update(Version $version): void
    {
        $directory = $this->getOption('directory');
        $process = new Process(['composer', 'config', 'version', $version->format()], $directory);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ErrorException(
                'Could not update composer version',
                0,
                1,
                __FILE__,
                __LINE__,
                new ErrorException($process->getErrorOutput())
            );
        }
    }

}