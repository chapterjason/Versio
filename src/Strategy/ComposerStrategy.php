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
use Symfony\Component\Process\Process;
use Versio\Version\Version;
use function dirname;

class ComposerStrategy extends AbstractStrategy
{

    /**
     * @param Version $version
     * @throws ErrorException
     */
    public function update(Version $version): void
    {
        $this->validateOptions();
        $file = $this->getFile();

        $process = new Process(['composer', 'config', 'version', $version->format()], dirname($file));
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

    /**
     * @throws ErrorException
     */
    public function validateOptions(): void
    {
        $file = $this->getFile();

        if (!file_exists($file)) {
            throw new ErrorException('Expected composer file "' . $file . '" does not exists.');
        }

    }

    /**
     * @return string
     * @throws ErrorException
     */
    protected function getFile(): string
    {
        return $this->getOption('directory', getcwd()) . '/composer.json';
    }
}