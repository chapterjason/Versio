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
use Versio\Utils;
use Versio\Version\Version;

class Release extends AbstractVersionCommand
{

    protected static $defaultName = 'release';

    protected function configure(): void
    {
        $this->setDescription('Creates a release.')
            ->addArgument(
                'master',
                InputArgument::OPTIONAL,
                'Next version on master'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws ErrorException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $version = $this->getVersion();

        if ($this->isMaster()) {
            $masterType = strtolower($input->getArgument('master'));
            $this->validateMasterBranchRelease();
            $this->validateMaster($masterType);

            $releaseBranchName = 'release/' . $version->getMajor() . '.' . $version->getMinor();

            // Prepare release version
            $releaseVersion = Version::clone($version);
            $this->versionManager->unsetType($releaseVersion);
            $this->versionManager->setDev($releaseVersion, false);

            // Prepare next version
            $nextVersion = Version::clone($releaseVersion);
            $this->versionManager->incrementPatch($nextVersion);

            // Prepare master version
            $masterVersion = Version::clone($releaseVersion);
            if ($masterType === 'major') {
                $this->versionManager->incrementMajor($masterVersion);
            } else if ($masterType === 'minor') {
                $this->versionManager->incrementMinor($masterVersion);
            }

            $this->createReleaseBranch(
                $output,
                $releaseBranchName,
                $releaseVersion,
                $nextVersion,
                $masterVersion
            );
        } else {
            $currentBranch = $this->shell->currentBranch();
            $this->validateReleaseBranchRelease($currentBranch);

            // Prepare release version
            $releaseVersion = Version::clone($version);
            $this->versionManager->unsetType($releaseVersion);
            $this->versionManager->setDev($releaseVersion, false);

            // Prepare next version
            $nextVersion = Version::clone($releaseVersion);
            $this->versionManager->incrementPatch($nextVersion);
            $this->versionManager->setDev($nextVersion, true);

            $this->createRelease($output, $currentBranch, $releaseVersion, $nextVersion);
        }
    }

    /**
     * @throws ErrorException
     */
    private function validateMasterBranchRelease(): void
    {
        $version = $this->getVersion();
        $workflow = $this->getWorkflow();
        $type = $this->getType();
        $transition = $this->getTransition($type, 'release');

        if (!$workflow->can($version, $transition)) {
            // @todo
            throw new ErrorException('Can not create release from "' . strtolower($type) . '".');
        }
    }

    /**
     * @param string $currentBranch
     * @throws ErrorException
     */
    private function validateReleaseBranchRelease(string $currentBranch): void
    {
        $version = $this->getVersion();
        $workflow = $this->getWorkflow();

        if (!Utils::isReleaseBranch($currentBranch)) {
            // @todo
            throw new ErrorException('Can not create release on non release branch.');
        }

        if ($this->getType() === 'RELEASE') {
            // @todo
            throw new ErrorException('Can not release already released release branch.');
        }

        $type = $this->versionManager->getType($version);
        $transition = $this->getTransition($type, 'release');

        if (!$workflow->can($version, $transition)) {
            // @todo
            throw new ErrorException('Can not create release from "' . strtolower($type) . '".');
        }
    }

}