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
                    ->info('default locale to use with faker if none is specified in the expression')
                ->end()
                ->integerNode('seed')
                    ->defaultValue(1)
                    ->info('a seed  to make sure faker generates data consistently across runs, set to null to disable')
                ->end()
                ->scalarNode('logger')
                    ->defaultValue('logger')
                    ->info('ID of a service implementing the Psr\Log\LoggerInterface')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
