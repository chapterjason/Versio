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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Versio\Utils;
use Versio\Version\Version;

class Patch extends AbstractVersionCommand
{

    protected static $defaultName = 'patch';

    protected function configure(): void
    {
        $this->setDescription('Creates a patch.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws ErrorException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $version = $this->versioFile->getVersion();

        $currentBranch = $this->shell->currentBranch();
        if (!Utils::isReleaseBranch($currentBranch)) {
            throw new ErrorException('Can not create patch non release branch.');
        }

        if ($version->getPatch() === 0) {
            throw new ErrorException('Can not release patch on release branch in prerelease.');
        }

        // Prepare release version
        $releaseVersion = Version::clone($version);
        $this->versionManager->setDev($releaseVersion, false);

        // Prepare next version
        $nextVersion = Version::clone($releaseVersion);
        $this->versionManager->incrementPatch($nextVersion);
        $this->versionManager->setDev($nextVersion, true);

        $this->createRelease($output, $currentBranch, $releaseVersion, $nextVersion);
    }

}