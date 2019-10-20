<?php
/**
 * This file is part of the Versio package.
 *
 * (c) Jason Schilling <jason.schilling@sourecode.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * File that was distributed with this source code.
 */

namespace App\Tests\Versio;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Versio\Exception\InvalidVersionException;
use Versio\Version\Version;

class VersionTest extends TestCase
{

    /**
     * @dataProvider getTestGetBuildResults
     */
    public function testGetBuild($value, $expected)
    {
        $version = Version::parse($value);

        $this->assertSame($expected, $version->getBuild());
    }

    public function getTestGetBuildResults()
    {
        return [
            ['0.0.4', []],
            ['1.2.3', []],
            ['10.20.30', []],
            ['1.1.2-prerelease+meta', ['meta']],
            ['1.1.2+meta', ['meta']],
            ['1.1.2+meta-valid', ['meta-valid']],
            ['1.0.0-alpha', []],
            ['1.0.0-beta', []],
            ['1.0.0-alpha.beta', []],
            ['1.0.0-alpha.beta.1', []],
            ['1.0.0-alpha.1', []],
            ['1.0.0-alpha0.valid', []],
            ['1.0.0-alpha.0valid', []],
            ['1.0.0-alpha-a.b-c-somethinglong+build.1-aef.1-its-okay', ['build', '1-aef', '1-its-okay']],
            ['1.0.0-rc.1+build.1', ['build', '1']],
            ['2.0.0-rc.1+build.123', ['build', '123']],
            ['1.2.3-beta', []],
            ['10.2.3-DEV-SNAPSHOT', []],
            ['1.2.3-SNAPSHOT-123', []],
            ['1.0.0', []],
            ['2.0.0', []],
            ['1.1.7', []],
            ['2.0.0+build.1848', ['build', '1848']],
            ['2.0.1-alpha.1227', []],
            ['1.0.0-alpha+beta', ['beta']],
            ['1.2.3----RC-SNAPSHOT.12.9.1--.12+788', ['788']],
            ['1.2.3----R-S.12.9.1--.12+meta', ['meta']],
            ['1.2.3----RC-SNAPSHOT.12.9.1--.12', []],
            ['1.0.0+0.build.1-rc.10000aaa-kk-0.1', ['0', 'build', '1-rc', '10000aaa-kk-0', '1']],
            ['9223372036854775807.9223372036854775807.9223372036854775807', []],
            ['1.0.0-0A.is.legal', []],
        ];
    }

    /**
     * @dataProvider getTestGetPrereleaseResults
     */
    public function testGetPrerelease($value, $expected)
    {
        $version = Version::parse($value);

        $this->assertSame($expected, $version->getPrerelease());
    }

    public function getTestGetPrereleaseResults()
    {
        return [
            ['0.0.4', []],
            ['1.2.3', []],
            ['10.20.30', []],
            ['1.1.2-prerelease+meta', ['prerelease']],
            ['1.1.2+meta', []],
            ['1.1.2+meta-valid', []],
            ['1.0.0-alpha', ['alpha']],
            ['1.0.0-beta', ['beta']],
            ['1.0.0-alpha.beta', ['alpha', 'beta']],
            ['1.0.0-alpha.beta.1', ['alpha', 'beta', '1']],
            ['1.0.0-alpha.1', ['alpha', '1']],
            ['1.0.0-alpha0.valid', ['alpha0', 'valid']],
            ['1.0.0-alpha.0valid', ['alpha', '0valid']],
            ['1.0.0-alpha-a.b-c-somethinglong+build.1-aef.1-its-okay', ['alpha-a', 'b-c-somethinglong']],
            ['1.0.0-rc.1+build.1', ['rc', '1']],
            ['2.0.0-rc.1+build.123', ['rc', '1']],
            ['1.2.3-beta', ['beta']],
            ['10.2.3-DEV-SNAPSHOT', ['DEV-SNAPSHOT']],
            ['1.2.3-SNAPSHOT-123', ['SNAPSHOT-123']],
            ['1.0.0', []],
            ['2.0.0', []],
            ['1.1.7', []],
            ['2.0.0+build.1848', []],
            ['2.0.1-alpha.1227', ['alpha', '1227']],
            ['1.0.0-alpha+beta', ['alpha']],
            ['1.2.3----RC-SNAPSHOT.12.9.1--.12+788', ['---RC-SNAPSHOT', '12', '9', '1--', '12']],
            ['1.2.3----R-S.12.9.1--.12+meta', ['---R-S', '12', '9', '1--', '12']],
            ['1.2.3----RC-SNAPSHOT.12.9.1--.12', ['---RC-SNAPSHOT', '12', '9', '1--', '12']],
            ['1.0.0+0.build.1-rc.10000aaa-kk-0.1', []],
            ['9223372036854775807.9223372036854775807.9223372036854775807', []],
            ['1.0.0-0A.is.legal', ['0A', 'is', 'legal']],
        ];
    }

    /**
     * @dataProvider getTestGetPatchResults
     */
    public function testGetPatch($value, $expected)
    {
        $version = Version::parse($value);

        $this->assertSame($expected, $version->getPatch());
    }

    public function getTestGetPatchResults()
    {
        return [
            ['0.0.4', 4],
            ['1.2.3', 3],
            ['10.20.30', 30],
            ['1.1.2-prerelease+meta', 2],
            ['1.1.2+meta', 2],
            ['1.1.2+meta-valid', 2],
            ['1.0.0-alpha', 0],
            ['1.0.0-beta', 0],
            ['1.0.0-alpha.beta', 0],
            ['1.0.0-alpha.beta.1', 0],
            ['1.0.0-alpha.1', 0],
            ['1.0.0-alpha0.valid', 0],
            ['1.0.0-alpha.0valid', 0],
            ['1.0.0-alpha-a.b-c-somethinglong+build.1-aef.1-its-okay', 0],
            ['1.0.0-rc.1+build.1', 0],
            ['2.0.0-rc.1+build.123', 0],
            ['1.2.3-beta', 3],
            ['10.2.3-DEV-SNAPSHOT', 3],
            ['1.2.3-SNAPSHOT-123', 3],
            ['1.0.0', 0],
            ['2.0.0', 0],
            ['1.1.7', 7],
            ['2.0.0+build.1848', 0],
            ['2.0.1-alpha.1227', 1],
            ['1.0.0-alpha+beta', 0],
            ['1.2.3----RC-SNAPSHOT.12.9.1--.12+788', 3],
            ['1.2.3----R-S.12.9.1--.12+meta', 3],
            ['1.2.3----RC-SNAPSHOT.12.9.1--.12', 3],
            ['1.0.0+0.build.1-rc.10000aaa-kk-0.1', 0],
            ['9223372036854775807.9223372036854775807.9223372036854775807', 9223372036854775807],
            ['1.0.0-0A.is.legal', 0],
        ];
    }

    /**
     * @dataProvider getTestGetMinorResults
     */
    public function testGetMinor($value, $expected)
    {
        $version = Version::parse($value);

        $this->assertSame($expected, $version->getMinor());
    }

    public function getTestGetMinorResults()
    {
        return [
            ['0.0.4', 0],
            ['1.2.3', 2],
            ['10.20.30', 20],
            ['1.1.2-prerelease+meta', 1],
            ['1.1.2+meta', 1],
            ['1.1.2+meta-valid', 1],
            ['1.0.0-alpha', 0],
            ['1.0.0-beta', 0],
            ['1.0.0-alpha.beta', 0],
            ['1.0.0-alpha.beta.1', 0],
            ['1.0.0-alpha.1', 0],
            ['1.0.0-alpha0.valid', 0],
            ['1.0.0-alpha.0valid', 0],
            ['1.0.0-alpha-a.b-c-somethinglong+build.1-aef.1-its-okay', 0],
            ['1.0.0-rc.1+build.1', 0],
            ['2.0.0-rc.1+build.123', 0],
            ['1.2.3-beta', 2],
            ['10.2.3-DEV-SNAPSHOT', 2],
            ['1.2.3-SNAPSHOT-123', 2],
            ['1.0.0', 0],
            ['2.0.0', 0],
            ['1.1.7', 1],
            ['2.0.0+build.1848', 0],
            ['2.0.1-alpha.1227', 0],
            ['1.0.0-alpha+beta', 0],
            ['1.2.3----RC-SNAPSHOT.12.9.1--.12+788', 2],
            ['1.2.3----R-S.12.9.1--.12+meta', 2],
            ['1.2.3----RC-SNAPSHOT.12.9.1--.12', 2],
            ['1.0.0+0.build.1-rc.10000aaa-kk-0.1', 0],
            ['9223372036854775807.9223372036854775807.9223372036854775807', 9223372036854775807],
            ['1.0.0-0A.is.legal', 0],
        ];
    }

    /**
     * @dataProvider getTestSetMinorResults
     */
    public function testSetMinor($version, $minor, $expected)
    {
        $version = Version::parse($version);
        $version->setMinor($minor);
        $this->assertSame($expected, $version->format());
    }

    public function getTestSetMinorResults()
    {
        return [
            ['0.0.4', 1, '0.1.4'],
            ['1.2.3', 3, '1.3.3'],
            ['10.20.30', 11, '10.11.30'],
            ['1.1.2-prerelease+meta', 2, '1.2.2-prerelease+meta'],
            ['1.1.2+meta', 2, '1.2.2+meta'],
            ['1.1.2+meta-valid', 2, '1.2.2+meta-valid'],
            ['1.0.0-alpha', 2, '1.2.0-alpha'],
            ['1.0.0-beta', 2, '1.2.0-beta'],
            ['1.0.0-alpha.beta', 2, '1.2.0-alpha.beta'],
            ['1.0.0-alpha.beta.1', 2, '1.2.0-alpha.beta.1'],
            ['1.0.0-alpha.1', 2, '1.2.0-alpha.1'],
            ['1.0.0-alpha0.valid', 2, '1.2.0-alpha0.valid'],
            ['1.0.0-alpha.0valid', 2, '1.2.0-alpha.0valid'],
            [
                '1.0.0-alpha-a.b-c-somethinglong+build.1-aef.1-its-okay',
                2,
                '1.2.0-alpha-a.b-c-somethinglong+build.1-aef.1-its-okay',
            ],
            ['1.0.0-rc.1+build.1', 2, '1.2.0-rc.1+build.1'],
            ['2.0.0-rc.1+build.123', 3, '2.3.0-rc.1+build.123'],
            ['1.2.3-beta', 3, '1.3.3-beta'],
            ['10.2.3-DEV-SNAPSHOT', 11, '10.11.3-DEV-SNAPSHOT'],
            ['1.2.3-SNAPSHOT-123', 3, '1.3.3-SNAPSHOT-123'],
            ['1.0.0', 2, '1.2.0'],
            ['2.0.0', 3, '2.3.0'],
            ['1.1.7', 2, '1.2.7'],
            ['2.0.0+build.1848', 3, '2.3.0+build.1848'],
            ['2.0.1-alpha.1227', 3, '2.3.1-alpha.1227'],
            ['1.0.0-alpha+beta', 2, '1.2.0-alpha+beta'],
            ['1.2.3----RC-SNAPSHOT.12.9.1--.12+788', 3, '1.3.3----RC-SNAPSHOT.12.9.1--.12+788'],
            ['1.2.3----R-S.12.9.1--.12+meta', 3, '1.3.3----R-S.12.9.1--.12+meta'],
            ['1.2.3----RC-SNAPSHOT.12.9.1--.12', 3, '1.3.3----RC-SNAPSHOT.12.9.1--.12'],
            ['1.0.0+0.build.1-rc.10000aaa-kk-0.1', 2, '1.2.0+0.build.1-rc.10000aaa-kk-0.1'],
            [
                '9223372036854775807.9223372036854775807.9223372036854775807',
                10,
                '9223372036854775807.10.9223372036854775807',
            ],
            ['1.0.0-0A.is.legal', 2, '1.2.0-0A.is.legal'],
        ];
    }

    /**
     * @dataProvider getTestSetMajorResults
     */
    public function testSetMajor($version, $major, $expected)
    {
        $version = Version::parse($version);
        $version->setMajor($major);

        $this->assertSame($expected, $version->format());
    }

    public function getTestSetMajorResults()
    {
        return [
            ['0.0.4', 1, '1.0.4'],
            ['1.2.3', 2, '2.2.3'],
            ['10.20.30', 11, '11.20.30'],
            ['1.1.2-prerelease+meta', 2, '2.1.2-prerelease+meta'],
            ['1.1.2+meta', 2, '2.1.2+meta'],
            ['1.1.2+meta-valid', 2, '2.1.2+meta-valid'],
            ['1.0.0-alpha', 2, '2.0.0-alpha'],
            ['1.0.0-beta', 2, '2.0.0-beta'],
            ['1.0.0-alpha.beta', 2, '2.0.0-alpha.beta'],
            ['1.0.0-alpha.beta.1', 2, '2.0.0-alpha.beta.1'],
            ['1.0.0-alpha.1', 2, '2.0.0-alpha.1'],
            ['1.0.0-alpha0.valid', 2, '2.0.0-alpha0.valid'],
            ['1.0.0-alpha.0valid', 2, '2.0.0-alpha.0valid'],
            [
                '1.0.0-alpha-a.b-c-somethinglong+build.1-aef.1-its-okay',
                2,
                '2.0.0-alpha-a.b-c-somethinglong+build.1-aef.1-its-okay',
            ],
            ['1.0.0-rc.1+build.1', 2, '2.0.0-rc.1+build.1'],
            ['2.0.0-rc.1+build.123', 3, '3.0.0-rc.1+build.123'],
            ['1.2.3-beta', 2, '2.2.3-beta'],
            ['10.2.3-DEV-SNAPSHOT', 11, '11.2.3-DEV-SNAPSHOT'],
            ['1.2.3-SNAPSHOT-123', 2, '2.2.3-SNAPSHOT-123'],
            ['1.0.0', 2, '2.0.0'],
            ['2.0.0', 3, '3.0.0'],
            ['1.1.7', 2, '2.1.7'],
            ['2.0.0+build.1848', 3, '3.0.0+build.1848'],
            ['2.0.1-alpha.1227', 3, '3.0.1-alpha.1227'],
            ['1.0.0-alpha+beta', 2, '2.0.0-alpha+beta'],
            ['1.2.3----RC-SNAPSHOT.12.9.1--.12+788', 2, '2.2.3----RC-SNAPSHOT.12.9.1--.12+788'],
            ['1.2.3----R-S.12.9.1--.12+meta', 2, '2.2.3----R-S.12.9.1--.12+meta'],
            ['1.2.3----RC-SNAPSHOT.12.9.1--.12', 2, '2.2.3----RC-SNAPSHOT.12.9.1--.12'],
            ['1.0.0+0.build.1-rc.10000aaa-kk-0.1', 2, '2.0.0+0.build.1-rc.10000aaa-kk-0.1'],
            [
                '9223372036854775807.9223372036854775807.9223372036854775807',
                10,
                '10.9223372036854775807.9223372036854775807',
            ],
            ['1.0.0-0A.is.legal', 2, '2.0.0-0A.is.legal'],
        ];
    }

    /**
     * @dataProvider getTestGetMajorResults
     */
    public function testGetMajor($value, $expected)
    {
        $version = Version::parse($value);

        $this->assertSame($expected, $version->getMajor());
    }

    public function getTestGetMajorResults()
    {
        return [
            ['0.0.4', 0],
            ['1.2.3', 1],
            ['10.20.30', 10],
            ['1.1.2-prerelease+meta', 1],
            ['1.1.2+meta', 1],
            ['1.1.2+meta-valid', 1],
            ['1.0.0-alpha', 1],
            ['1.0.0-beta', 1],
            ['1.0.0-alpha.beta', 1],
            ['1.0.0-alpha.beta.1', 1],
            ['1.0.0-alpha.1', 1],
            ['1.0.0-alpha0.valid', 1],
            ['1.0.0-alpha.0valid', 1],
            ['1.0.0-alpha-a.b-c-somethinglong+build.1-aef.1-its-okay', 1],
            ['1.0.0-rc.1+build.1', 1],
            ['2.0.0-rc.1+build.123', 2],
            ['1.2.3-beta', 1],
            ['10.2.3-DEV-SNAPSHOT', 10],
            ['1.2.3-SNAPSHOT-123', 1],
            ['1.0.0', 1],
            ['2.0.0', 2],
            ['1.1.7', 1],
            ['2.0.0+build.1848', 2],
            ['2.0.1-alpha.1227', 2],
            ['1.0.0-alpha+beta', 1],
            ['1.2.3----RC-SNAPSHOT.12.9.1--.12+788', 1],
            ['1.2.3----R-S.12.9.1--.12+meta', 1],
            ['1.2.3----RC-SNAPSHOT.12.9.1--.12', 1],
            ['1.0.0+0.build.1-rc.10000aaa-kk-0.1', 1],
            ['9223372036854775807.9223372036854775807.9223372036854775807', 9223372036854775807],
            ['1.0.0-0A.is.legal', 1],
        ];
    }

    /**
     * @dataProvider getTestParseResults
     */
    public function testParse($value)
    {
        $result = Version::parse($value);

        $this->assertSame($value, $result->format());
    }

    public function getTestParseResults()
    {
        return [
            ['0.0.4'],
            ['1.2.3'],
            ['10.20.30'],
            ['1.1.2-prerelease+meta'],
            ['1.1.2+meta'],
            ['1.1.2+meta-valid'],
            ['1.0.0-alpha'],
            ['1.0.0-beta'],
            ['1.0.0-alpha.beta'],
            ['1.0.0-alpha.beta.1'],
            ['1.0.0-alpha.1'],
            ['1.0.0-alpha0.valid'],
            ['1.0.0-alpha.0valid'],
            ['1.0.0-alpha-a.b-c-somethinglong+build.1-aef.1-its-okay'],
            ['1.0.0-rc.1+build.1'],
            ['2.0.0-rc.1+build.123'],
            ['1.2.3-beta'],
            ['10.2.3-DEV-SNAPSHOT'],
            ['1.2.3-SNAPSHOT-123'],
            ['1.0.0'],
            ['2.0.0'],
            ['1.1.7'],
            ['2.0.0+build.1848'],
            ['2.0.1-alpha.1227'],
            ['1.0.0-alpha+beta'],
            ['1.2.3----RC-SNAPSHOT.12.9.1--.12+788'],
            ['1.2.3----R-S.12.9.1--.12+meta'],
            ['1.2.3----RC-SNAPSHOT.12.9.1--.12'],
            ['1.0.0+0.build.1-rc.10000aaa-kk-0.1'],
            ['9223372036854775807.9223372036854775807.9223372036854775807'],
            ['1.0.0-0A.is.legal'],
        ];
    }

    /**
     * @dataProvider getTestParseErrorResults
     */
    public function testParseErrors($value)
    {
        try {
            Version::parse($value);
            $this->fail('Should throw a InvalidVersionException');
        } catch (InvalidVersionException $e) {
            $this->assertStringMatchesFormat('Invalid version "' . $value . '".', $e->getMessage());
        }
    }

    public function getTestParseErrorResults()
    {
        return [
            ['1'],
            ['1.2'],
            ['1.2.3-0123'],
            ['1.2.3-0123.0123'],
            ['1.1.2+.123'],
            ['+invalid'],
            ['-invalid'],
            ['-invalid+invalid'],
            ['-invalid.01'],
            ['alpha'],
            ['alpha.beta'],
            ['alpha.beta.1'],
            ['alpha.1'],
            ['alpha+beta'],
            ['alpha_beta'],
            ['alpha.'],
            ['alpha..'],
            ['beta'],
            ['1.0.0-alpha_beta'],
            ['-alpha.'],
            ['1.0.0-alpha..'],
            ['1.0.0-alpha..1'],
            ['1.0.0-alpha...1'],
            ['1.0.0-alpha....1'],
            ['1.0.0-alpha.....1'],
            ['1.0.0-alpha......1'],
            ['1.0.0-alpha.......1'],
            ['01.1.1'],
            ['1.01.1'],
            ['1.1.01'],
            ['1.2'],
            ['1.2.3.DEV'],
            ['1.2-SNAPSHOT'],
            ['1.2.31.2.3----RC-SNAPSHOT.12.09.1--..12+788'],
            ['1.2-RC-SNAPSHOT'],
            ['-1.0.3-gamma+b7718'],
            ['+justmeta'],
            ['9.8.7+meta+meta'],
            ['9.8.7-whatever+meta+meta'],
            ['9223372036854775807.9223372036854775807.9223372036854775807----RC-SNAPSHOT.12.09.1--------------------------------..12'],
        ];
    }

}
