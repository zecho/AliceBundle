<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * The configuration of the bundle.
 *
 * @author Baldur Rensch <brensch@gmail.com>
 */
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
