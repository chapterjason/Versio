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
use ReflectionException;
use Symfony\Component\Finder\Finder;
use Versio\Version\Version;
use Versio\Version\VersionReplacer;

class ExpressionStrategy extends AbstractStrategy
{

    /**
     * @param Version $version
     * @throws ErrorException
     * @throws ReflectionException
     */
    public function update(Version $version): void
    {
        $files = $this->getFiles();
        $replacer = new VersionReplacer($version);

        $expression = $this->getOption('expression');
        $replacement = $this->getOption('replacement');

        $expression = '/' . str_replace('{{SEMVER}}', Version::$expression, $expression) . '/';
        $replacement = $replacer->replace($replacement);

        foreach ($files as $file) {
            $content = $file->getContents();
            $content = preg_replace($expression, $replacement, $content);
            file_put_contents($file->getRealPath(), $content);
        }
    }

    /**
     * @throws ErrorException
     * @throws ReflectionException
     */
    protected function getFiles()
    {
        $finder = new Finder();
        $finder->files()->name($this->getOption('file'));

        return $finder->getIterator();
    }

}