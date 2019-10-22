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

use ErrorException;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ComposerStrategyConfiguration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('composer');

        $rootNode = $treeBuilder->getRootNode();

        // @formatter:off
        $rootNode
            ->children()
                ->scalarNode('directory')
                    ->validate()
                        ->always(static function (string $value){
                            $file = $value . '/composer.json';
                            if(!file_exists($file)){
                                throw new ErrorException('Expected composer file "' . $file . '" does not exists.');
                            }
                        })
                    ->end()
                    ->cannotBeEmpty()
                    ->defaultValue(getcwd())
                ->end() // directory
            ->end();
        // @formatter:on

        return $treeBuilder;
    }

}