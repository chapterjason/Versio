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

class NpmStrategy extends AbstractStrategy
{

    /**
     * @param Version $version
     * @throws ErrorException
     * @throws ReflectionException
     */
    public function update(Version $version): void
    {
        $directory = $this->getOption('directory');

        $npm = $this->commandExists('npm');
        $yarn = $this->commandExists('yarn');

        if (!$npm && !$yarn) {
            throw new ErrorException('Could not found command "npm" or command "yarn".');
        }

        $command = $yarn ? ['yarn', 'version', '--no-git-tag-version', '--new-version'] : [
            'npm',
            'version',
            '--no-git-tag-version',
        ];
        $command[] = $version->format();

        $process = new Process($command, $directory);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ErrorException(
                'Could not update version in package.json',
                0,
                1,
                __FILE__,
                __LINE__,
                new ErrorException($process->getErrorOutput())
            );
        }
    }

    private function commandExists(string $command): bool
    {
        $isWindows = strpos(PHP_OS, 'WIN') === 0;
        $testCommand = $isWindows ? 'where' : 'command -v';

        return is_executable(trim(shell_exec($testCommand . ' ' . $command)));
    }

}