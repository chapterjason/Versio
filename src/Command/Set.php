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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Versio\Version\Version;

class Set extends AbstractVersionCommand
{

    protected static $defaultName = 'set';

    protected function configure(): void
    {
        $this->setDescription('Sets the version.')
            ->setHelp('Sets the version in all configured places.')
            ->addArgument('value', InputArgument::REQUIRED, 'Version to set');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $value = $input->getArgument('value');
        $inputVersion = Version::Parse($value);

        $this->versioFile->setVersion($inputVersion);
        $this->versioFile->save();
    }

}