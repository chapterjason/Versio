<?php
/**
 * This file is part of the Versio package.
 *
 * (c) Jason Schilling <jason.schilling@sourecode.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * File that was distributed with this source code.
 */

namespace Versio\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class LineStrategyConfiguration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('line');

        $rootNode = $treeBuilder->getRootNode();

        // @formatter:off
        $rootNode
            ->children()
                ->arrayNode('directories')
                    ->defaultValue([getcwd()])
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('pattern')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->integerNode('line')
                    ->min(1)
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('replacement')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end();
        // @formatter:on

        return $treeBuilder;
    }

}