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
use Versio\Version\VersioFile;

class Init extends AbstractVersionCommand
{

    protected static $defaultName = 'init';

    protected function configure(): void
    {
        $this->setDescription('Initialize versioning to current project.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws ErrorException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->shell->isRepository()) {
            $this->shell->initialize();
            $this->shell->trackAll();
            $this->shell->commit('Add initial set of files');
        }

        if (!$this->shell->isClean()) {
            throw new ErrorException('Current working tree is not clean.');
        }

        $configuration = $this->getBasicConfiguration();
        $versioFile = new VersioFile($configuration);
        $this->versioFileManager->set($versioFile);
        $this->versioFileManager->save();

        $this->shell->trackAll();
        $this->shell->commit('Add versio file');
    }

    private function getBasicConfiguration(): array
    {
        return [
            'version' => '0.1.0-DEV',
            'workflow' => [
                'places' => [
                    'beta',
                ],
                'transitions' => [
                    'master' => [
                        'beta',
                    ],
                    'beta' => [
                        'beta',
                        'release',
                    ],
                ],
            ],
        ];
    }

}