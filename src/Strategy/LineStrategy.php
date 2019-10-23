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
use Symfony\Component\Finder\SplFileInfo;
use Versio\Version\Version;
use Versio\Version\VersionReplacer;

class LineStrategy extends AbstractStrategy
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

        $lineNumber = $this->getOption('line');
        $replacement = $this->getOption('replacement');
        $replacement = $replacer->replace($replacement);

        foreach ($files as $file) {
            $content = file_get_contents($file);
            $lines = preg_split("/\r?\n/", $content);
            array_splice($lines, $lineNumber - 1, 1, $replacement);
            $content = implode("\n", $lines);
            file_put_contents($file, $content);
        }
    }

    /**
     * @return string[]
     * @throws ErrorException
     * @throws ReflectionException
     */
    public function getFiles(): array
    {
        $pattern = $this->getOption('pattern');
        $directories = $this->getOption('directories');

        $finder = new Finder();
        $finder->files()->in($directories)->name($pattern);

        return array_map(
            static function ($item) {
                /**
                 * @var SplFileInfo $item
                 */
                return $item->getPathname();
            },
            iterator_to_array($finder->getIterator(), false)
        );
    }

}