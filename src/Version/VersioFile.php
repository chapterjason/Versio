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
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Workflow\Workflow;
use Versio\Configuration\VersioFileConfiguration;
use Versio\Workflow\WorkflowGenerator;
use function file_put_contents;
use function getcwd;
use function json_encode;

class VersioFile
{

    /**
     * @var array
     */
    private $configuration;

    /**
     * @var WorkflowGenerator
     */
    private $workflowGenerator;

    /**
     * @var Workflow $workflow
     */
    private $workflow;

    /**
     * @var string $directory
     */
    private $directory;

    /**
     * @var string $file
     */
    private $file;

    /**
     * @var object
     */
    private $data;

    /**
     * @var string
     */
    private $locatedFile;

    /**
     * VersioFile constructor.
     * @param WorkflowGenerator $workflowGenerator
     * @param string|null $directory
     * @param string $file
     * @throws ErrorException
     */
    public function __construct(
        WorkflowGenerator $workflowGenerator,
        string $directory = null,
        string $file = 'versio.json'
    ) {
        $this->workflowGenerator = $workflowGenerator;

        $this->directory = $directory;

        if (!$this->directory) {
            $currentDirectory = getcwd();
            if (!$currentDirectory) {
                // @todo
                throw new ErrorException('Could not locate working directory');
            }
            $this->directory = $currentDirectory;
        }

        $this->file = $file;
    }

    public function getVersion(): Version
    {
        $configuration = $this->getConfiguration();

        return Version::parse($configuration['version']);
    }

    private function getConfiguration(): array
    {
        if (!$this->configuration) {
            $data = $this->getData();
            $configuration = new VersioFileConfiguration();
            $processor = new Processor();

            $this->configuration = $processor->processConfiguration($configuration, [$data]);
        }

        return $this->configuration;
    }

    private function getData()
    {
        if (!$this->data) {
            $file = $this->getFile();
            $this->data = json_decode(file_get_contents($file), true);
        }

        return $this->data;
    }

    private function getFile()
    {
        if (!$this->locatedFile) {
            $locator = new FileLocator([$this->directory]);

            $this->locatedFile = $locator->locate($this->file);
        }

        return $this->locatedFile;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
        $encoded = json_encode($data);
        file_put_contents(getcwd() . '/' . $this->file, $encoded);
    }

    public function setVersion(Version $version): VersioFile
    {
        $this->configuration['version'] = $this->data['version'] = $version->format();

        return $this;
    }

    public function save(): VersioFile
    {
        $file = $this->getFile();
        file_put_contents($file, json_encode($this->data, JSON_PRETTY_PRINT));

        return $this;
    }

    /**
     * @return Workflow
     * @throws ErrorException
     */
    public function getWorkflow(): Workflow
    {
        if (!$this->workflow) {
            $configuration = $this->getConfiguration();
            $this->workflow = $this->workflowGenerator->generate($configuration['workflow']);
        }

        return $this->workflow;
    }

}