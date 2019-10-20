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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Workflow\Transition;
use Versio\Utils;
use Versio\Version\Version;

class Prerelease extends AbstractVersionCommand
{

    protected static $defaultName = 'prerelease';

    protected function configure(): void
    {
        $this->setDescription('Creates a prerelease.')
            ->setHelp('Creates a prerelease based on the given params.')
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'Prerelease type'
            )
            ->addArgument(
                'next',
                InputArgument::REQUIRED,
                'Next prerelease type.'
            )
            ->addArgument(
                'master',
                InputArgument::OPTIONAL,
                'Next version on master'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws ErrorException
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $places = $this->getPlaces();

        if (count($places) <= 0) {
            throw new ErrorException("Can not release prerelease if there are no places configured.");
        }

        $type = $input->getArgument('type');
        $next = $input->getArgument('next');

        if (null === $type) {
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                'Please select the prerelease version you want to release',
                $places
            );
            $question->setErrorMessage('Prerelease version %s is invalid.');

            $type = $helper->ask($input, $output, $question);
            $input->setArgument('type', $type);
        }

        if (null === $next) {
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                'Please select the prerelease version you want to release next',
                $places
            );
            $question->setErrorMessage('Next prerelease version %s is invalid.');

            $next = $helper->ask($input, $output, $question);
            $input->setArgument('next', $next);
        }


        parent::interact($input, $output);
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws ErrorException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $prereleaseType = strtolower($input->getArgument('type'));
        $nextType = strtolower($input->getArgument('next'));

        if ($this->getType() === 'RELEASE') {
            // @todo
            throw new ErrorException('Can not prerelease already released release branch.');
        }

        $this->validateTransition($prereleaseType, $nextType);
        $version = $this->getVersion();

        // Prepare prerelease version
        $prereleaseVersion = Version::clone($version);
        $currentType = $this->versionManager->getType($prereleaseVersion);
        if ($prereleaseType !== strtolower($currentType)) {
            $this->versionManager->setType($prereleaseVersion, strtoupper($prereleaseType));
        }
        $this->versionManager->setDev($prereleaseVersion, false);

        // Prepare next version
        $nextVersion = Version::clone($prereleaseVersion);
        $this->versionManager->setDev($nextVersion, true);

        if ($prereleaseType === $nextType) {
            $this->versionManager->incrementType($nextVersion);
        } else {
            $this->versionManager->setType($nextVersion, strtoupper($nextType));
        }

        if ($this->isMaster()) {
            $masterType = strtolower($input->getArgument('master'));
            $this->validateMaster($masterType);
            $releaseBranchName = 'release/' . $version->getMajor() . '.' . $version->getMinor();

            // Prepare master version
            $masterVersion = Version::clone($version);
            if ($masterType === 'major') {
                $this->versionManager->incrementMajor($masterVersion);
            } else if ($masterType === 'minor') {
                $this->versionManager->incrementMinor($masterVersion);
            }

            $this->createReleaseBranch(
                $output,
                $releaseBranchName,
                $prereleaseVersion,
                $nextVersion,
                $masterVersion
            );
        } else {
            $currentBranch = $this->shell->currentBranch();

            if (!Utils::isReleaseBranch($currentBranch)) {
                // @todo
                throw new ErrorException('Current branch "' . $currentBranch . '" is not a release branch.');
            }

            if (strtolower($this->getType()) !== $prereleaseType) {
                throw new ErrorException(
                    'Can not prerelease "' . $prereleaseType . '" on a "' . strtolower($this->getType()) . '" version.'
                );
            }

            $this->createRelease($output, $currentBranch, $prereleaseVersion, $nextVersion);
        }
    }

    /**
     * @param string $type
     * @param string $next
     * @throws ErrorException
     */
    private function validateTransition(string $type, string $next): void
    {
        $places = $this->getPlaces();

        if (!in_array($type, $places, true)) {
            // @todo
            throw new ErrorException('Prerelease "' . $type . '" is not configured.');
        }

        if (!in_array($next, $places, true)) {
            // @todo
            throw new ErrorException('Next prerelease "' . $next . '" is not configured.');
        }

        $version = $this->getVersion();
        $workflow = $this->getWorkflow();
        $definition = $workflow->getDefinition();
        $transitions = array_map(
            static function (Transition $item) {
                return $item->getName();
            },
            $definition->getTransitions()
        );

        $releaseTransition = $this->getTransition($this->getType(), $type);
        $nextTransition = $this->getTransition($type, $next);

        // @todo not sure if this is necessary cause of the next workflow->can check.
        if (!in_array($releaseTransition, $transitions, true)) {
            // @todo
            throw new ErrorException('Prerelease "' . $type . '" is invalid.');
        }

        if (!$workflow->can($version, $releaseTransition)) {
            // @todo
            throw new ErrorException('Prerelease "' . $type . '" is invalid.');
        }

        if (!in_array($nextTransition, $transitions, true)) {
            // @todo
            throw new ErrorException('Can not create prerelease "' . $next . '" after "' . $type . '".');
        }

    }

}