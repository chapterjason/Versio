<?php
/**
 * This file is part of the Versio package.
 *
 * (c) Jason Schilling <jason.schilling@sourecode.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * File that was distributed with this source code.
 */

namespace Versio\Tests\Strategy;

use org\bovigo\vfs\vfsStream;
use Versio\Strategy\ExpressionStrategy;
use Versio\Tests\TestCase;
use Versio\Version\Version;

class ExpressionStrategyTest extends TestCase
{

    public function testUpdate()
    {
        $expectedFile = $this->getUrl('ExpressionExpected.php.txt');
        $resultFile = $this->getUrl('ExpressionInput.php.txt');
        $version = Version::parse('1.0.0-ALPHA.1+build.005');
        $strategy = new ExpressionStrategy();

        $strategy->setOptions(
            [
                'directories' => [$this->root->url()],
                'pattern' => 'ExpressionInput.php.txt',
                'expression' => "version = '{{SEMVER}}';",
                'replacement' => "version = '{{VERSION}}';",
            ]
        );

        $strategy->update($version);

        $this->assertFileEquals($expectedFile, $resultFile);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->root = vfsStream::copyFromFileSystem(__DIR__ . '/Fixture/ExpressionStrategy', $this->root);
    }


}