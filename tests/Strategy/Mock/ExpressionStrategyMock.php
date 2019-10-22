<?php
/**
 * This file is part of the Versio package.
 *
 * (c) Jason Schilling <jason.schilling@sourecode.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * File that was distributed with this source code.
 */

namespace Versio\Tests\Strategy\Mock;

use Symfony\Component\Finder\Finder;
use Versio\Strategy\ExpressionStrategy;

class ExpressionStrategyMock extends ExpressionStrategy
{

    protected function getFiles()
    {
        $finder = new Finder();

        $finder->files()->in(__DIR__ . '/../Fixture')->name('ExpressionInput.php.txt');

        return $finder->getIterator();
    }

}