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
use Versio\Version\VersionReplacer;

class VersionReplacerTest extends TestCase
{

    /**
     * @var Version $version
     */
    protected $version;

    /**
     * @dataProvider getTestReplaceData
     */
    public function testReplace($value, $expected)
    {
        $replacer = new VersionReplacer($this->version);
        $result = $replacer->replace($value);

        $this->assertSame($expected, $result);
    }

    public function getTestReplaceData()
    {
        return [
            ['This is {{VERSION_MAJOR}} an apple.', 'This is 0 an apple.'],
            ['This is {{VERSION_MINOR}} an apple.', 'This is 1 an apple.'],
            ['This is {{VERSION_PATCH}} an apple.', 'This is 0 an apple.'],
            ['This is {{VERSION_PRERELEASE}} an apple.', 'This is BETA.1-DEV an apple.'],
            ['This is {{VERSION_BUILD}} an apple.', 'This is build.001.sha.9c55dd1 an apple.'],
            ['This is {{VERSION}} an apple.', 'This is 0.1.0-BETA.1-DEV+build.001.sha.9c55dd1 an apple.'],
        ];
    }

    protected function setUp()
    {
        parent::setUp();
        $this->version = Version::parse('0.1.0-BETA.1-DEV+build.001.sha.9c55dd1');
    }


}
