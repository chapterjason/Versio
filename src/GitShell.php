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
            $this->throwException($process, 'Could not checkout branch "' . $branch . '".');
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
     * @param Process $process
     * @param string $message
     * @throws ErrorException
     */
    private function throwException(Process $process, string $message): void
    {
        throw new ErrorException(
            $message,
            0, 1, __FILE__, __LINE__,
            new ErrorException($process->getErrorOutput())
        );
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
            $this->throwException($process, 'Could not track all changes');
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
            $this->throwException($process, 'Could not create commit.');
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
            $this->throwException($process, 'Could not retrieve branches.');
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
            $this->throwException($process, 'Could not create branch "' . $branch . '".');
        }

        return $process;
    }

    /**
     * @return bool
     */
    public function isRepository(): bool
    {
        $process = $this->execute(['git', 'tag']);

        return $process->isSuccessful();
    }

    /**
     * @return Process
     * @throws ErrorException
     */
    public function initialize(): Process
    {
        $process = $this->execute(['git', 'init']);

        if (!$process->isSuccessful()) {
            // @todo
            throw new ErrorException(
                'Could not initialize repository.',
                0,
                new ErrorException($process->getErrorOutput())
            );
        }

        return $process;
    }

    /**
     * @return bool
     * @throws ErrorException
     */
    public function isClean(): bool
    {
        $process = $this->execute(["git", "status", "--porcelain"]);

        if (!$process->isSuccessful()) {
            // @todo
            throw new ErrorException(
                'Could not check repository status.',
                0,
                new ErrorException($process->getErrorOutput())
            );
        }

        $output = trim($process->getOutput());

        return $output === '';
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
            $this->throwException($process, 'Could not create tag "' . $tag . '".');
        }

        return $process;
    }

}