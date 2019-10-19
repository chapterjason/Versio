<?php
/**
 * This file is part of the Versio package.
 *
 * (c) Jason Schilling <jason.schilling@sourecode.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * File that was distributed with this source code.
 */

namespace Versio\Command;

use ErrorException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Versio\GitShell;
use Versio\Version\VersioFile;
use Versio\Version\Version;
use Versio\Version\VersionManager;
use function strtoupper;

abstract class AbstractVersionCommand extends Command
{

    /**
     * @var VersioFile $versioFile
     */
    protected $versioFile;

    /**
     * @var VersionManager $versionManager
     */
    protected $versionManager;

    /**
     * @var GitShell $shell
     */
    protected $shell;

    /**
     * Alpha constructor.
     * @param GitShell $shell
     * @param VersionManager $versionManager
     * @param VersioFile $versioFile
     */
    public function __construct(GitShell $shell, VersionManager $versionManager, VersioFile $versioFile)
    {
        $this->versionManager = $versionManager;
        $this->versioFile = $versioFile;
        $this->shell = $shell;
        parent::__construct();
    }

    protected function getTransition(string $from, string $to): string
    {
        return strtoupper($from) . '_' . strtoupper($to);
    }

    protected function getType()
    {
        $version = $this->versioFile->getVersion();

        if ($version->getPatch() >= 1) {
            return 'RELEASE';
        }

        return $this->versionManager->getType($version) ?? 'MASTER';
    }

    /**
     * @return array
     * @throws ErrorException
     */
    protected function getPlaces(): array
    {
        $workflow = $this->versioFile->getWorkflow();
        $definition = $workflow->getDefinition();
        $places = array_map(
            static function ($item) {
                return strtolower($item);
            },
            array_filter(
                $definition->getPlaces(),
                static function ($item) {
                    return $item !== 'MASTER' && $item !== 'RELEASE';
                }
            )
        );

        return $places;
    }

    /**
     * @return bool
     */
    protected function isMaster(): bool
    {
        $version = $this->versioFile->getVersion();
        $branch = $this->shell->currentBranch();

        return 'master' === $branch &&
            null === $this->versionManager->getType($version) &&
            $this->versionManager->isDev($version) &&
            0 === $version->getPatch();
    }

    /**
     * @param string $master
     * @throws ErrorException
     */
    protected function validateMaster(string $master): void
    {
        if (!in_array($master, ['major', 'minor'], true)) {
            throw new ErrorException('Next master version "' . $master . '" is invalid.');
        }
    }

    /**
     * @param OutputInterface $output
     * @param string $branchName
     * @param Version $masterVersion
     * @param Version $releaseVersion
     * @param Version $nextVersion
     * @throws ErrorException
     */
    protected function createReleaseBranch(
        OutputInterface $output,
        string $branchName,
        Version $releaseVersion,
        Version $nextVersion,
        Version $masterVersion
    ): void {
        if ($this->shell->branchExists($branchName)) {
            throw new ErrorException('Release branch "' . $branchName . '" already exists.');
        }

        $this->shell->createBranch($branchName);

        $output->writeln('Bump version on branch "master" to "' . $masterVersion->format() . '".');
        $this->shell->checkout('master');
        $this->bump($masterVersion);

        $this->shell->checkout($branchName);
        $this->createRelease($output, $branchName, $releaseVersion, $nextVersion);
    }

    /**
     * @param Version $masterBranchVersion
     * @throws ErrorException
     */
    protected function bump(Version $masterBranchVersion): void
    {
        $this->versioFile->setVersion($masterBranchVersion);
        $this->versioFile->save();

        $this->shell->trackAll();
        $this->shell->commit('Bump version to ' . $masterBranchVersion->format());
    }

    /**
     * @param OutputInterface $output
     * @param Version $releaseVersion
     * @param string $currentBranch
     * @param Version $nextVersion
     * @throws ErrorException
     */
    protected function createRelease(
        OutputInterface $output,
        string $currentBranch,
        Version $releaseVersion,
        Version $nextVersion
    ): void {
        $output->writeln(
            'Release version "' . $releaseVersion->format() . '" on branch "' . $currentBranch . '".'
        );
        $this->release($releaseVersion);

        // $releaseType
        $output->writeln(
            'Bump version on branch "' . $currentBranch . '" to "' . $nextVersion->format() . '".'
        );
        $this->bump($nextVersion);
    }

    /**
     * @param Version $version
     * @throws ErrorException
     */
    protected function release(Version $version): void
    {
        $this->versioFile->setVersion($version);
        $this->versioFile->save();

        $this->shell->trackAll();
        $this->shell->commit('Update version for ' . $version->format());
        $this->shell->createTag('v' . $version->format());
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed|string|string[]|null
     */
    protected function getMasterType(InputInterface $input, OutputInterface $output)
    {
        $masterType = $input->getArgument('master');

        if (null === $masterType) {
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                'Please select the next major verison',
                ['minor', 'major']
            );
            $question->setErrorMessage('Next major version %s is invalid.');

            $masterType = $helper->ask($input, $output, $question);
        }

        if (null === $masterType) {
            throw new InvalidArgumentException('Missing parameter "master"');
        }

        return $masterType;
    }


}