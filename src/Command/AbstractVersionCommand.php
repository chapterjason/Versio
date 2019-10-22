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
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Workflow\Workflow;
use Versio\GitShell;
use Versio\Strategy\StrategyManager;
use Versio\Version\VersioFile;
use Versio\Version\VersioFileManager;
use Versio\Version\Version;
use Versio\Version\VersionManager;

abstract class AbstractVersionCommand extends Command
{

    /**
     * @var StrategyManager $strategyManager
     */
    protected $strategyManager;

    /**
     * @var VersioFileManager $versioFileManager
     */
    protected $versioFileManager;

    /**
     * @var VersionManager $versionManager
     */
    protected $versionManager;
    /**
     * @var GitShell $shell
     */
    protected $shell;
    /**
     * @var VersioFile $versioFile
     */
    private $versioFile;

    /**
     * Alpha constructor.
     * @param GitShell $shell
     * @param VersionManager $versionManager
     * @param VersioFileManager $versioFileManager
     * @param StrategyManager $strategyManager
     */
    public function __construct(
        GitShell $shell,
        VersionManager $versionManager,
        VersioFileManager $versioFileManager,
        StrategyManager $strategyManager
    ) {
        $this->shell = $shell;
        $this->versionManager = $versionManager;
        $this->versioFileManager = $versioFileManager;
        $this->strategyManager = $strategyManager;
        parent::__construct();
    }

    protected function getTransition(string $from, string $to): string
    {
        return strtoupper($from) . '_' . strtoupper($to);
    }

    /**
     * @return string|null
     * @throws ErrorException
     */
    protected function getType(): ?string
    {
        $version = $this->getVersioFile()->getVersion();

        if ($version->getPatch() >= 1) {
            return 'RELEASE';
        }

        return $this->versionManager->getType($version) ?? 'MASTER';
    }

    /**
     * @return VersioFile
     * @throws ErrorException
     */
    protected function getVersioFile(): VersioFile
    {
        if (!$this->versioFile) {
            $this->versioFile = $this->versioFileManager->load();
        }

        return $this->versioFile;
    }

    /**
     * @return string[]
     * @throws ErrorException
     */
    protected function getPlaces(): array
    {
        $workflow = $this->getWorkflow();
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

        return array_values($places);
    }

    /**
     * @return Workflow
     * @throws ErrorException
     */
    protected function getWorkflow(): Workflow
    {
        $versioFile = $this->getVersioFile();

        return $this->versioFileManager->getWorkflow($versioFile);
    }

    /**
     * @return Version
     * @throws ErrorException
     */
    protected function getVersion(): Version
    {
        $versioFile = $this->getVersioFile();

        return $versioFile->getVersion();
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
     * @param Version $version
     * @throws ErrorException
     */
    protected function bump(Version $version): void
    {
        $this->strategyManager->update($this->getVersioFile(), $version);

        $this->shell->trackAll();
        $this->shell->commit('Bump version to ' . $version->format());
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
        $this->strategyManager->update($this->getVersioFile(), $version);

        $this->shell->trackAll();
        $this->shell->commit('Update version for ' . $version->format());
        $this->shell->createTag('v' . $version->format());
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws ErrorException
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if ($this->getDefinition()->hasArgument('master')) {
            // if expression can not be merged into parent.
            // The expression should only run if the argument master is present,
            // so it means it is not the init command, which does not require the versio file.
            if ($this->isMaster()) {
                $masterType = $input->getArgument('master');

                if (null === $masterType) {
                    $helper = $this->getHelper('question');
                    $question = new ChoiceQuestion(
                        'Please select the next master version',
                        ['minor', 'major']
                    );
                    $question->setErrorMessage('Next master version "%s" is invalid.');

                    $masterType = $helper->ask($input, $output, $question);
                }

                if (null === $masterType) {
                    throw new InvalidArgumentException('Missing parameter "master"');
                }

                $input->setArgument('master', $masterType);
            }
        }
    }

    /**
     * @return bool
     * @throws ErrorException
     */
    protected function isMaster(): bool
    {
        $version = $this->getVersioFile()->getVersion();
        $branch = $this->shell->currentBranch();

        return 'master' === $branch &&
            null === $this->versionManager->getType($version) &&
            $this->versionManager->isDev($version) &&
            0 === $version->getPatch();
    }

}