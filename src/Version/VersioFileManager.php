<?php
/**
 * This file is part of the Versio package.
 *
 * (c) Jason Schilling <jason.schilling@sourecode.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * File that was distributed with this source code.
 */

namespace Versio\Version;

use ErrorException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Workflow\Workflow;
use Versio\Configuration\VersioFileConfiguration;
use Versio\Workflow\WorkflowGenerator;

class VersioFileManager
{

    /**
     * @var WorkflowGenerator $workflowGenerator
     */
    protected $workflowGenerator;

    /**
     * @var Filesystem $filesystem
     */
    protected $filesystem;

    /**
     * VersioFileManager constructor.
     * @param WorkflowGenerator $workflowGenerator
     */
    public function __construct(WorkflowGenerator $workflowGenerator)
    {
        $this->workflowGenerator = $workflowGenerator;
        $this->filesystem = new Filesystem();
    }

    public function save(VersioFile $versioFile): VersioFileManager
    {
        $configuration = $versioFile->getConfiguration();
        $encoded = json_encode($configuration, JSON_PRETTY_PRINT);
        $this->filesystem->dumpFile($this->getFile(), $encoded);

        return $this;
    }

    private function getFile(): string
    {
        return getcwd() . '/versio.json';
    }

    public function load(): VersioFile
    {
        $data = file_get_contents($this->getFile());
        $decoded = json_decode($data, true);

        $configuration = new VersioFileConfiguration();
        $processor = new Processor();

        $configuration = $processor->processConfiguration($configuration, [$decoded]);

        return new VersioFile($configuration);
    }

    public function exists(): bool
    {
        return $this->filesystem->exists($this->getFile());
    }

    /**
     * @param VersioFile $versioFile
     * @return Workflow
     * @throws ErrorException
     */
    public function getWorkflow(VersioFile $versioFile): Workflow
    {
        return $this->workflowGenerator->generate($versioFile->getConfiguration()['workflow']);
    }

}