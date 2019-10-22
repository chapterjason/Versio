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

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Versio\Tests\Strategy\Mock\ExpressionStrategyMock;
use Versio\Version\Version;

class ExpressionStrategyTest extends TestCase
{

    public function testUpdate()
    {
        $strategy = new ExpressionStrategyMock();

        $strategy->setOptions(
            [
                'files' => [],
                'expression' => "version = '{{SEMVER}}';",
                'replacement' => "version = '{{VERSION}}';",
            ]
        );

        $version = Version::parse('1.0.0-ALPHA.1+build.005');
        $strategy->update($version);

        $expected = $this->getExpected();
        $result = $this->getOutput();

        $this->assertSame($expected, $result);
    }

    protected function getExpected()
    {
        return file_get_contents(__DIR__ . '/Fixture/ExpressionExpected.php.txt');
    }

    protected function getOutput()
    {
        return file_get_contents(__DIR__ . '/Fixture/ExpressionInput.php.txt');
    }

    protected function setUp()
    {
        parent::setUp();
        $content = file_get_contents(__DIR__ . '/Fixture/ExpressionInput.php.txt.tpl');
        file_put_contents(__DIR__ . '/Fixture/ExpressionInput.php.txt', $content);
    }


}