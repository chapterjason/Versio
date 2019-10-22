<?php
/**
 * This file is part of the Versio package.
 *
 * (c) Jason Schilling <jason.schilling@sourecode.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * File that was distributed with this source code.
 */

namespace Versio\Tests;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Versio\Utils;

class UtilsTest extends TestCase
{

    /**
     * @dataProvider getStrictParseIntTestData
     */
    public function testStrictParseInt($value, $expected)
    {
        $result = Utils::strictParseInt($value);

        if (is_nan($expected)) {
            $this->assertTrue(is_nan($result));
        } else {
            $this->assertSame($expected, $result);
        }
    }

    public function getStrictParseIntTestData()
    {
        return [
            ['1', 1],
            ['10000', 10000],
            ['0001', 1],
            ['--1', NAN],
            ['-1', NAN],
            ['+1', NAN],
            ['a1', NAN],
            ['1a', NAN],
            ['foo', NAN],
        ];
    }

    /**
     * @dataProvider getConvertToExtraData
     */
    public function testConvertToExtra($value, $expected)
    {
        $result = Utils::convertToExtra($value);

        $this->assertSame($expected, $result);
    }

    public function getConvertToExtraData()
    {
        return [
            [['FOO', 'BAR'], [['FOO', 'BAR']]],
            [['FOO-BAR', 'BAZZ'], [['FOO'], ['BAR', 'BAZZ']]],
            [['FOO.BAR', 'BAZZ'], [['FOO', 'BAR', 'BAZZ']]],
            [['FOO-BAR', 'BAZZ-FOZ'], [['FOO'], ['BAR', 'BAZZ'], ['FOZ']]],
        ];
    }

    /**
     * @dataProvider getConvertToPrereleaseData
     */
    public function testConvertToPrerelease($value, $expected)
    {
        $result = Utils::convertToPrerelease($value);

        $this->assertSame($expected, $result);
    }

    public function getConvertToPrereleaseData()
    {
        return [
            [[['FOO', 'BAR']], ['FOO', 'BAR']],
            [[['FOO'], ['BAR', 'BAZZ']], ['FOO-BAR', 'BAZZ']],
            [[['FOO', 'BAR', 'BAZZ']], ['FOO', 'BAR', 'BAZZ']],
            [[['FOO'], ['BAR', 'BAZZ'], ['FOZ']], ['FOO-BAR', 'BAZZ-FOZ']],
        ];
    }

}
