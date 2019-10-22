<?php
/**
 * This file is part of the Versio package.
 *
 * (c) Jason Schilling <jason.schilling@sourecode.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * File that was distributed with this source code.
 */

namespace Versio;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Versio\DependencyInjection\CompilerPass\CommandLoaderPass;

class CustomKernel extends DefaultKernel
{

    /**
     * @param ContainerBuilder $container
     */
    protected function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CommandLoaderPass());
    }

}
