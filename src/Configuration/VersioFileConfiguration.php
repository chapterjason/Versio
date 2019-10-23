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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Versio\Exception\InvalidVersionException;
use Versio\Version\Version;

class VersioFileConfiguration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('versio');

        $rootNode = $treeBuilder->getRootNode();

        $this->addVersionSection($rootNode);
        $this->addStrategySection($rootNode);
        $this->addWorkflowSection($rootNode);

        return $treeBuilder;
    }

    private function addVersionSection(ArrayNodeDefinition $rootNode): void
    {
        // @formatter:off
        $rootNode
            ->children()
                ->scalarNode('version')
                    ->beforeNormalization()
                        ->always()
                        ->then(static function ($value) {
                            try {
                                Version::parse($value);
                                return $value;
                            } catch (InvalidVersionException $exception){
                                return null;
                            }
                        })
                    ->end()
                    ->isRequired()
                    ->validate()
                        ->always()
                        ->ifNull()
                        ->thenInvalid('Invalid version.')
                    ->end()
                ->end() // version
            ->end();
        // @formatter:on
    }

    private function addStrategySection(ArrayNodeDefinition $rootNode): void
    {
        // @formatter:off
        $rootNode
            ->children()
                ->arrayNode('strategies')
                    ->arrayPrototype()
                        ->children()
                            ->enumNode('type')
                                ->isRequired()
                                ->cannotBeEmpty()
                                ->values(['versio', 'composer', 'expression', 'line', 'npm'])
                            ->end()
                            ->arrayNode('options')
                                ->ignoreExtraKeys(false)
                            ->end()
                        ->end()
                    ->end()
                ->end() // strategies

            ->end();
        // @formatter:on
    }

    private function addWorkflowSection(ArrayNodeDefinition $rootNode): void
    {
        // @formatter:off
        $rootNode
            ->children()
                ->arrayNode('workflow')
                    ->children()
                        ->append($this->addPlacesNode())
                        ->append($this->addTransitionsNode())
                    ->end()
                ->end() // workflow
            ->end();
        // @formatter:on
    }


    private function addPlacesNode()
    {
        $treeBuilder = new TreeBuilder('places');

        // @formatter:off
        $node = $treeBuilder->getRootNode()
            ->beforeNormalization()
                ->always()
                ->then(static function ($places) {
                    return array_map('strtoupper', $places);
                })
            ->end()
            ->scalarPrototype()
                ->validate()
                    ->always()
                    ->ifInArray(['MASTER', 'RELEASE'])
                    ->thenInvalid('Can not defined place "MASTER" or "RELEASE"')
                    ->ifNotInArray(['ALPHA', 'BETA', 'RC', 'RTM'])
                    ->thenInvalid('Invalid place. Valid places are "ALPHA", "BETA", "RC" or "RTM"')
                ->end()
            ->end();
        // @formatter:on

        return $node;
    }

    private function addTransitionsNode()
    {
        $treeBuilder = new TreeBuilder('transitions');

        // @formatter:off
        $node = $treeBuilder->getRootNode()
            ->beforeNormalization()
                ->always()
                ->then(static function ($configuredTransitions) {
                    $transitions = [];

                    foreach ($configuredTransitions as $key => $value){
                        $transitions[strtoupper($key)] = array_map('strtoupper', $value);
                    }

                    return $transitions;
                })
            ->end()
            ->validate()
                ->always()
                ->ifTrue(static function($items){
                    $keys = array_keys($items);
                    return in_array('RELEASE', $keys,false);
                })
                ->thenInvalid('Can not start transitioning from "RELEASE"')
                ->ifTrue(static function($items){
                    $keys = array_keys($items);
                    foreach ($keys as $key){
                        if(!in_array($key, ['ALPHA', 'BETA', 'RC', 'RTM', 'MASTER'],false)){
                            return true;
                        }
                    }

                    return false;
                })
                ->thenInvalid('Invalid transitioning. Valid start transitions are "ALPHA", "BETA", "RC", "RTM" or "MASTER"')
            ->end()
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->arrayPrototype()
                ->scalarPrototype()
                    ->validate()
                        ->always()
                        ->ifInArray(['MASTER'])
                        ->thenInvalid('Can not transitioning to "MASTER"')
                        ->ifNotInArray(['ALPHA', 'BETA', 'RC', 'RTM', 'RELEASE'])
                        ->thenInvalid('Invalid transitioning. Valid transitions are "ALPHA", "BETA", "RC", "RTM", "RELEASE"')
                    ->end()
                ->end()
            ->end();
        // @fortmatter:on

        return $node;
    }
}