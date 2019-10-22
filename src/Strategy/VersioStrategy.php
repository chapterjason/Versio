<?php
/**
 * This file is part of the Versio package.
 *
 * (c) Jason Schilling <jason.schilling@sourecode.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * File that was distributed with this source code.
 */

namespace Versio\Strategy;

use ErrorException;
use Versio\Version\VersioFileManager;
use Versio\Version\Version;

class VersioStrategy extends AbstractStrategy
{

    /**
     * @var VersioFileManager $versioFileManager
     */
    protected $versioFileManager;

    /**
     * VersioStrategy constructor.
     * @param VersioFileManager $versioFileManager
     */
    public function __construct(VersioFileManager $versioFileManager)
    {
        $this->versioFileManager = $versioFileManager;
    }

    /**
     * @param Version $version
     * @throws ErrorException
     */
    public function update(Version $version): void
    {
        $this->validateOptions();
        $file = $this->getFile();

        $versioFile = $this->versioFileManager->load($file);
        $versioFile->setVersion($version);
        $this->versioFileManager->save($versioFile, $file);
    }

    /**
     * @throws ErrorException
     */
    public function validateOptions(): void
    {
        $file = $this->getFile();

        if (!file_exists($file)) {
            throw new ErrorException('Expected versio file "' . $file . '" does not exists.');
        }

    }

    /**
     * @return string
     * @throws ErrorException
     */
    protected function getFile(): string
    {
        return $this->getOption('directory', getcwd()) . '/versio.json';
    }
}