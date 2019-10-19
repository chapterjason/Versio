<?php
/**
 * This file is part of the Versio package.
 *
 * (c) Jason Schilling <jason.schilling@sourecode.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * File that was distributed with this source code.
 */

namespace Versio;

use ErrorException;
use Symfony\Component\Process\Process;

class GitShell
{

    /**
     * @param string $branch
     * @return Process
     * @throws ErrorException
     */
    public function checkout(string $branch): Process
    {
        $process = $this->execute(['git', 'checkout', $branch]);

        if (!$process->isSuccessful()) {
            // @todo
            throw new ErrorException(
                'Could not checkout branch "' . $branch . '".',
                0,
                new ErrorException($process->getErrorOutput())
            );
        }

        return $process;
    }

    /**
     * @param string[] $command
     * @return Process
     */
    public function execute(array $command): Process
    {
        $process = new Process($command);
        $process->run();

        return $process;
    }

    /**
     * @return Process
     * @throws ErrorException
     */
    public function trackAll(): Process
    {
        $process = $this->execute(['git', 'add', '-A']);

        if (!$process->isSuccessful()) {
            // @todo
            throw new ErrorException('Could not track all changes', 0, new ErrorException($process->getErrorOutput()));
        }

        return $process;
    }

    /**
     * @param string $message
     * @return Process
     * @throws ErrorException
     */
    public function commit(string $message): Process
    {
        $process = $this->execute(['git', 'commit', '-m', $message]);

        if (!$process->isSuccessful()) {
            // @todo
            throw new ErrorException('Could not create commit.', 0, new ErrorException($process->getErrorOutput()));
        }

        return $process;
    }

    public function currentBranch(): string
    {
        $process = $this->execute(['git', 'symbolic-ref', '--short', 'HEAD']);

        return trim($process->getOutput());
    }

    /**
     * @param string $branch
     * @return bool
     * @throws ErrorException
     */
    public function branchExists(string $branch): bool
    {
        $process = $this->execute(['git', 'branch']);

        if (!$process->isSuccessful()) {
            // @todo
            throw new ErrorException(
                'Could not retrieve branches."', 0, new ErrorException($process->getErrorOutput())
            );
        }

        $branches = preg_split("/\r?\n/", trim($process->getOutput()));

        $branches = array_map(
            function ($branch) {
                return preg_replace("/^\*?\s+|\s+$/", '', $branch);
            },
            $branches
        );

        return in_array($branch, $branches, true);
    }

    /**
     * @param string $branch
     * @return Process
     * @throws ErrorException
     */
    public function createBranch(string $branch): Process
    {
        $process = $this->execute(['git', 'checkout', '-b', $branch]);

        if (!$process->isSuccessful()) {
            // @todo
            throw new ErrorException(
                'Could not create branch "' . $branch . '".',
                0,
                new ErrorException($process->getErrorOutput())
            );
        }

        return $process;
    }

    /**
     * @param string $tag
     * @return Process
     * @throws ErrorException
     */
    public function createTag(string $tag): Process
    {
        $process = $this->execute(['git', 'tag', $tag]);

        if (!$process->isSuccessful()) {
            // @todo
            throw new ErrorException(
                'Could not create tag "' . $tag . '".',
                0,
                new ErrorException($process->getErrorOutput())
            );
        }

        return $process;
    }

}