<?php

declare(strict_types=1);

namespace Dvc\ContaoContainerWrapperBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('container_wrapper');

        $treeBuilder->getRootNode()
            ->normalizeKeys(false)
            ->children()
                ->arrayNode('container')
                    ->normalizeKeys(false)
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('label')->end()
                            ->scalarNode('class')->end()
                            ->variableNode('children')->end()
                            ->variableNode('variants')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('groups')
                    ->normalizeKeys(false)
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('label')->end()
                            ->scalarNode('class')->end()
                            ->variableNode('children')->end()
                            ->variableNode('variants')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
