<?php
/**
 * This file is part of the Versio package.
 *
 * (c) Jason Schilling <jason.schilling@sourecode.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * File that was distributed with this source code.
 */

namespace Versio\DependencyInjection\CompilerPass;


use InvalidArgumentException;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\TypedReference;
use Versio\Command\AbstractVersionCommand;

class CommandLoaderPass implements CompilerPassInterface
{

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $lazyCommandMap = [];
        $lazyCommandRefs = [];

        $commandServices = $container->findTaggedServiceIds('console.command', true);

        foreach ($commandServices as $id => $tags) {
            $definition = $container->getDefinition($id);
            $class = $container->getParameterBag()->resolveValue($definition->getClass());


            if (!$r = $container->getReflectionClass($class)) {
                throw new InvalidArgumentException(
                    sprintf('Class "%s" used for service "%s" cannot be found.', $class, $id)
                );
            }

            if ($r->isSubclassOf(AbstractVersionCommand::class)) {
                $lazyCommandMap[$class::getDefaultName()] = $id;
                $lazyCommandRefs[$id] = new TypedReference($id, $class);
            }
        }

        $container->register("custom.command_loader", ContainerCommandLoader::class)
            ->setPublic(true)
            ->setArguments([ServiceLocatorTagPass::register($container, $lazyCommandRefs), $lazyCommandMap]);
    }
}