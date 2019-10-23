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

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{

    /**
     * @var  vfsStreamDirectory
     */
    protected $root;

    protected function getUrl(string $name): string
    {
        return $this->root->getChild($name)->url();
    }

    protected function setUp()
    {
        $this->root = vfsStream::setup('root');
    }

}