<?php

namespace Hautelook\AliceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('hautelook_alice');

        $rootNode
            ->children()
                ->scalarNode('locale')
                    ->defaultValue('en_US')
                    ->info('Locale to use with faker')
                ->end()
                ->integerNode('seed')
                    ->defaultValue(1)
                    ->info('A seed to make sure faker generates data consistently across runs, set to null to disable')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
