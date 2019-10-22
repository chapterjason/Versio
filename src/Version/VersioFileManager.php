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
     * @param Filesystem $filesystem
     */
    public function __construct(WorkflowGenerator $workflowGenerator, Filesystem $filesystem)
    {
        $this->workflowGenerator = $workflowGenerator;
        $this->filesystem = $filesystem;
    }

    /**
     * @param VersioFile $versioFile
     * @param string|null $file
     * @return VersioFileManager
     */
    public function save(VersioFile $versioFile, string $file = null): VersioFileManager
    {
        if (null === $file) {
            $file = $this->getFile();
        }

        $configuration = $versioFile->getConfiguration();

        if (0 === count($configuration['strategies'])) {
            unset($configuration['strategies']);
        }

        $encoded = json_encode($configuration, JSON_PRETTY_PRINT);
        $this->filesystem->dumpFile($file, $encoded);

        return $this;
    }

    /**
     * @return string
     */
    protected function getFile(): string
    {
        return getcwd() . '/versio.json';
    }

    /**
     * @param string|null $file
     * @return VersioFile
     * @throws ErrorException
     */
    public function load(string $file = null): VersioFile
    {
        if (null === $file) {
            $file = $this->getFile();
        }

        if (!$this->exists($file)) {
            throw new ErrorException('Missing versio file.');
        }

        $data = file_get_contents($file);
        $decoded = json_decode($data, true);

        $configuration = new VersioFileConfiguration();
        $processor = new Processor();

        $configuration = $processor->processConfiguration($configuration, [$decoded]);

        return new VersioFile($configuration);
    }

    /**
     * @param string|null $file
     * @return bool
     */
    public function exists(string $file = null): bool
    {
        if (null === $file) {
            $file = $this->getFile();
        }

        return $this->filesystem->exists($file);
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