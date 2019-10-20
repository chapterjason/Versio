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

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Workflow\Workflow;
use Versio\Configuration\VersioFileConfiguration;
use Versio\Workflow\WorkflowGenerator;
use function file_get_contents;
use function getcwd;
use function json_decode;
use function json_encode;

class VersioFileManager
{

    /**
     * @var VersioFile $versioFile
     */
    protected $versioFile;

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

    public function save(): VersioFileManager
    {
        $configuration = $this->versioFile->getConfiguration();
        $encoded = json_encode($configuration, null, '    ');
        $this->filesystem->dumpFile($this->getFile(), $encoded);

        return $this;
    }

    private function getFile(): string
    {
        return getcwd() . '/versio.json';
    }

    public function get(): VersioFile
    {
        if (!$this->versioFile) {
            $this->load();
        }

        return $this->versioFile;
    }

    public function load(): VersioFileManager
    {
        $data = file_get_contents($this->get());
        $decoded = json_decode($data, true);

        $configuration = new VersioFileConfiguration();
        $processor = new Processor();

        $configuration = $processor->processConfiguration($configuration, [$decoded]);
        $this->versioFile = new VersioFile($configuration);

        return $this;
    }

    public function exists(): bool
    {
        return $this->filesystem->exists($this->getFile());
    }

    public function set(VersioFile $versioFile): VersioFileManager
    {
        $this->versioFile = $versioFile;

        return $this;
    }

    /**
     * @param VersioFile|null $versioFile
     * @return Workflow
     * @throws \ErrorException
     */
    public function getWorkflow(VersioFile $versioFile = null)
    {
        if (null === $versioFile) {
            $this->load();
            $versioFile = $this->versioFile;
        }

        return $this->workflowGenerator->generate($versioFile->getConfiguration()['workflow']);
    }

}