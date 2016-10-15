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
    const ORM_DRIVER = 'orm';
    const MONGODB_DRIVER = 'mongodb';
    const PHPCR_DRIVER = 'phpcr';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('hautelook_alice');

        $rootNode
            ->children()
                ->arrayNode('db_drivers')
                    ->info('The list of enabled drivers.')
                    ->addDefaultsIfNotSet()
                    ->cannotBeOverwritten()
                    ->children()
                        ->booleanNode(self::ORM_DRIVER)
                            ->defaultValue(null)
                        ->end()
                        ->booleanNode(self::MONGODB_DRIVER)
                            ->defaultValue(null)
                        ->end()
                        ->booleanNode(self::PHPCR_DRIVER)
                            ->defaultValue(null)
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('locale')
                    ->defaultValue('en_US')
                    ->info('Locale to use with faker')
                ->end()
                ->scalarNode('seed')
                    ->defaultValue(1)
                    ->info('A seed to make sure faker generates data consistently across runs, set to null to disable')
                ->end()
                ->booleanNode('persist_once')
                    ->defaultValue(false)
                    ->info('Only persist objects once if multiple files are passed')
                ->end()
                ->scalarNode('loading_limit')
                    ->defaultValue(5)
                    ->info('Maximum number of time the loader will try to load the files passed')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
