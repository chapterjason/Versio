<?php
/**
 * This file is part of the Versio package.
 *
 * (c) Jason Schilling <jason.schilling@sourecode.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * File that was distributed with this source code.
 */

namespace Versio\Tests\Version;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Versio\Version\Version;
use Versio\Version\VersionManager;

class VersionManagerTest extends TestCase
{

    /**
     * @dataProvider getSetDevData
     */
    public function testSetDev($value, $expectedTrue, $expectedFalse)
    {
        $versionManager = new VersionManager();
        $version = Version::parse($value);

        $versionManager->setDev($version, true);
        $this->assertSame($expectedTrue, $version->format());

        $versionManager->setDev($version, false);
        $this->assertSame($expectedFalse, $version->format());
    }

    public function getSetDevData()
    {
        return [
            ['0.1.0', '0.1.0-DEV', '0.1.0'],
            ['0.1.0-DEV', '0.1.0-DEV', '0.1.0'],
            ['0.1.0-ALPHA.1-DEV', '0.1.0-ALPHA.1-DEV', '0.1.0-ALPHA.1'],
            ['0.1.0-ALPHA.2-DEV', '0.1.0-ALPHA.2-DEV', '0.1.0-ALPHA.2'],
            ['0.1.0-BETA.1-DEV', '0.1.0-BETA.1-DEV', '0.1.0-BETA.1'],
            ['0.1.0-BETA.2-DEV', '0.1.0-BETA.2-DEV', '0.1.0-BETA.2'],
            ['0.1.0-RC.1-DEV', '0.1.0-RC.1-DEV', '0.1.0-RC.1'],
            ['0.1.0-RC.2-DEV', '0.1.0-RC.2-DEV', '0.1.0-RC.2'],
            ['0.1.0-RTM.1-DEV', '0.1.0-RTM.1-DEV', '0.1.0-RTM.1'],
            ['0.1.0-RTM.2-DEV', '0.1.0-RTM.2-DEV', '0.1.0-RTM.2'],
            ['0.1.0+build.001', '0.1.0-DEV+build.001', '0.1.0+build.001'],
            ['0.1.0-DEV+build.001', '0.1.0-DEV+build.001', '0.1.0+build.001'],
            ['0.1.0-ALPHA.1-DEV+build.001', '0.1.0-ALPHA.1-DEV+build.001', '0.1.0-ALPHA.1+build.001'],
            ['0.1.0-ALPHA.2-DEV+build.001', '0.1.0-ALPHA.2-DEV+build.001', '0.1.0-ALPHA.2+build.001'],
            ['0.1.0-BETA.1-DEV+build.001', '0.1.0-BETA.1-DEV+build.001', '0.1.0-BETA.1+build.001'],
            ['0.1.0-BETA.2-DEV+build.001', '0.1.0-BETA.2-DEV+build.001', '0.1.0-BETA.2+build.001'],
            ['0.1.0-RC.1-DEV+build.001', '0.1.0-RC.1-DEV+build.001', '0.1.0-RC.1+build.001'],
            ['0.1.0-RC.2-DEV+build.001', '0.1.0-RC.2-DEV+build.001', '0.1.0-RC.2+build.001'],
            ['0.1.0-RTM.1-DEV+build.001', '0.1.0-RTM.1-DEV+build.001', '0.1.0-RTM.1+build.001'],
            ['0.1.0-RTM.2-DEV+build.001', '0.1.0-RTM.2-DEV+build.001', '0.1.0-RTM.2+build.001'],
        ];
    }

    /**
     * @dataProvider getIsDevData
     */
    public function testIsDev($value, $expected)
    {
        $versionManager = new VersionManager();
        $version = Version::parse($value);

        $result = $versionManager->isDev($version);

        $this->assertSame($expected, $result);
    }

    public function getIsDevData()
    {
        return [
            ['0.1.0', false],
            ['0.1.0-ALPHA.1', false],
            ['0.1.0-BETA.1', false],
            ['0.1.0-RC.1', false],
            ['0.1.0-RTM.1', false],
            ['0.1.0-DEV', true],
            ['0.1.0-ALPHA.1-DEV', false],
            ['0.1.0-BETA.1-DEV', false],
            ['0.1.0-RC.1-DEV', false],
            ['0.1.0-RTM.1-DEV', false],
            ['0.1.0+build.001', false],
            ['0.1.0-ALPHA.1+build.001', false],
            ['0.1.0-BETA.1+build.001', false],
            ['0.1.0-RC.1+build.001', false],
            ['0.1.0-RTM.1+build.001', false],
            ['0.1.0-DEV+build.001', true],
            ['0.1.0-ALPHA.1-DEV+build.001', false],
            ['0.1.0-BETA.1-DEV+build.001', false],
            ['0.1.0-RC.1-DEV+build.001', false],
            ['0.1.0-RTM.1-DEV+build.001', false],
        ];
    }

    /**
     * @dataProvider getIsKindOfDevData
     */
    public function testIsKindOfDev($value, $expected)
    {
        $versionManager = new VersionManager();
        $version = Version::parse($value);

        $result = $versionManager->isKindOfDev($version);

        $this->assertSame($expected, $result);
    }

    public function getIsKindOfDevData()
    {
        return [
            ['0.1.0', false],
            ['0.1.0-ALPHA.1', false],
            ['0.1.0-BETA.1', false],
            ['0.1.0-RC.1', false],
            ['0.1.0-RTM.1', false],
            ['0.1.0-DEV', true],
            ['0.1.0-ALPHA.1-DEV', true],
            ['0.1.0-BETA.1-DEV', true],
            ['0.1.0-RC.1-DEV', true],
            ['0.1.0-RTM.1-DEV', true],
            ['0.1.0+build.001', false],
            ['0.1.0-ALPHA.1+build.001', false],
            ['0.1.0-BETA.1+build.001', false],
            ['0.1.0-RC.1+build.001', false],
            ['0.1.0-RTM.1+build.001', false],
            ['0.1.0-DEV+build.001', true],
            ['0.1.0-ALPHA.1-DEV+build.001', true],
            ['0.1.0-BETA.1-DEV+build.001', true],
            ['0.1.0-RC.1-DEV+build.001', true],
            ['0.1.0-RTM.1-DEV+build.001', true],
        ];
    }

    /**
     * @dataProvider getIsKindOfTypeData
     */
    public function testIsKindOfType($value, $expected)
    {
        $versionManager = new VersionManager();
        $version = Version::parse($value);

        $result = $versionManager->isKindOfType($version);

        $this->assertSame($expected, $result);
    }

    public function getIsKindOfTypeData()
    {
        return [
            ['0.1.0', false],
            ['0.1.0-ALPHA.1', true],
            ['0.1.0-BETA.1', true],
            ['0.1.0-RC.1', true],
            ['0.1.0-RTM.1', true],
            ['0.1.0-DEV', false],
            ['0.1.0-ALPHA.1-DEV', true],
            ['0.1.0-BETA.1-DEV', true],
            ['0.1.0-RC.1-DEV', true],
            ['0.1.0-RTM.1-DEV', true],
            ['0.1.0+build.001', false],
            ['0.1.0-ALPHA.1+build.001', true],
            ['0.1.0-BETA.1+build.001', true],
            ['0.1.0-RC.1+build.001', true],
            ['0.1.0-RTM.1+build.001', true],
            ['0.1.0-DEV+build.001', false],
            ['0.1.0-ALPHA.1-DEV+build.001', true],
            ['0.1.0-BETA.1-DEV+build.001', true],
            ['0.1.0-RC.1-DEV+build.001', true],
            ['0.1.0-RTM.1-DEV+build.001', true],
        ];
    }

    /**
     * @dataProvider getSetTypeData
     */
    public function testSetType($value, $type, $expected)
    {
        $versionManager = new VersionManager();
        $version = Version::parse($value);

        $versionManager->setType($version, $type);

        $this->assertSame($expected, $version->format());
    }

    public function getSetTypeData()
    {
        return [
            ['0.1.0', 'ALPHA', '0.1.0-ALPHA.1'],
            ['0.1.0-ALPHA.1', 'BETA', '0.1.0-BETA.1'],
            ['0.1.0-BETA.1', 'RC', '0.1.0-RC.1'],
            ['0.1.0-RC.1', 'RTM', '0.1.0-RTM.1'],
            ['0.1.0-RTM.1', 'ALPHA', '0.1.0-ALPHA.1'],
            ['0.1.0-DEV', 'BETA', '0.1.0-BETA.1-DEV'],
            ['0.1.0-ALPHA.1-DEV', 'RC', '0.1.0-RC.1-DEV'],
            ['0.1.0-BETA.1-DEV', 'RTM', '0.1.0-RTM.1-DEV'],
            ['0.1.0-RC.1-DEV', 'ALPHA', '0.1.0-ALPHA.1-DEV'],
            ['0.1.0-RTM.1-DEV', 'BETA', '0.1.0-BETA.1-DEV'],
            ['0.1.0+build.001', 'RC', '0.1.0-RC.1+build.001'],
            ['0.1.0-ALPHA.1+build.001', 'RTM', '0.1.0-RTM.1+build.001'],
            ['0.1.0-BETA.1+build.001', 'ALPHA', '0.1.0-ALPHA.1+build.001'],
            ['0.1.0-RC.1+build.001', 'BETA', '0.1.0-BETA.1+build.001'],
            ['0.1.0-RTM.1+build.001', 'RC', '0.1.0-RC.1+build.001'],
            ['0.1.0-DEV+build.001', 'RTM', '0.1.0-RTM.1-DEV+build.001'],
            ['0.1.0-ALPHA.1-DEV+build.001', 'BETA', '0.1.0-BETA.1-DEV+build.001'],
            ['0.1.0-BETA.1-DEV+build.001', 'ALPHA', '0.1.0-ALPHA.1-DEV+build.001'],
            ['0.1.0-RC.1-DEV+build.001', 'RTM', '0.1.0-RTM.1-DEV+build.001'],
            ['0.1.0-RTM.1-DEV+build.001', 'CUSTOM', '0.1.0-CUSTOM.1-DEV+build.001'],
        ];
    }

    /**
     * @dataProvider getIsTypeDevData
     */
    public function testIsTypeDev($value, $type, $expected)
    {
        $versionManager = new VersionManager();
        $version = Version::parse($value);

        $result = $versionManager->isTypeDev($version, $type);

        $this->assertSame($expected, $result);
    }

    public function getIsTypeDevData()
    {
        return [
            ['0.1.0', 'ALPHA',false],
            ['0.1.0-ALPHA.1','ALPHA', false],
            ['0.1.0-BETA.1','ALPHA', false],
            ['0.1.0-RC.1','ALPHA', false],
            ['0.1.0-RTM.1','ALPHA', false],
            ['0.1.0-DEV','ALPHA', false],
            ['0.1.0-ALPHA.1-DEV','ALPHA', true],
            ['0.1.0-BETA.1-DEV','ALPHA', false],
            ['0.1.0-RC.1-DEV','ALPHA', false],
            ['0.1.0-RTM.1-DEV','ALPHA', false],
            ['0.1.0+build.001','ALPHA', false],
            ['0.1.0-ALPHA.1+build.001','ALPHA', false],
            ['0.1.0-BETA.1+build.001','ALPHA', false],
            ['0.1.0-RC.1+build.001','ALPHA', false],
            ['0.1.0-RTM.1+build.001','ALPHA', false],
            ['0.1.0-DEV+build.001','ALPHA', false],
            ['0.1.0-ALPHA.1-DEV+build.001','ALPHA', true],
            ['0.1.0-BETA.1-DEV+build.001','ALPHA', false],
            ['0.1.0-RC.1-DEV+build.001','ALPHA', false],
            ['0.1.0-RTM.1-DEV+build.001','ALPHA', false],
        ];
    }

    /**
     * @dataProvider getIsTypeData
     */
    public function testIsType($value, $type, $expected)
    {
        $versionManager = new VersionManager();
        $version = Version::parse($value);

        $result = $versionManager->isType($version, $type);

        $this->assertSame($expected, $result);
    }

    public function getIsTypeData()
    {
        return [
            ['0.1.0', 'ALPHA',false],
            ['0.1.0-ALPHA.1','ALPHA', true],
            ['0.1.0-BETA.1','ALPHA', false],
            ['0.1.0-RC.1','ALPHA', false],
            ['0.1.0-RTM.1','ALPHA', false],
            ['0.1.0-DEV','ALPHA', false],
            ['0.1.0-ALPHA.1-DEV','ALPHA', false],
            ['0.1.0-BETA.1-DEV','ALPHA', false],
            ['0.1.0-RC.1-DEV','ALPHA', false],
            ['0.1.0-RTM.1-DEV','ALPHA', false],
            ['0.1.0+build.001','ALPHA', false],
            ['0.1.0-ALPHA.1+build.001','ALPHA', true],
            ['0.1.0-BETA.1+build.001','ALPHA', false],
            ['0.1.0-RC.1+build.001','ALPHA', false],
            ['0.1.0-RTM.1+build.001','ALPHA', false],
            ['0.1.0-DEV+build.001','ALPHA', false],
            ['0.1.0-ALPHA.1-DEV+build.001','ALPHA', false],
            ['0.1.0-BETA.1-DEV+build.001','ALPHA', false],
            ['0.1.0-RC.1-DEV+build.001','ALPHA', false],
            ['0.1.0-RTM.1-DEV+build.001','ALPHA', false],
        ];
    }
}
