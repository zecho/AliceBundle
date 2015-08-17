<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add registered Faker providers instances to the {@see Hautelook\AliceBundle\Faker\ProvidersChain}.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class ProviderCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $fakerDefinition = $container->findDefinition('hautelook_alice.faker');
        $providerChainDefinition = $container->findDefinition('hautelook_alice.faker.provider_chain');

        $providersIds = $container->findTaggedServiceIds('hautelook_alice.faker.provider');
        $providers = [];
        foreach ($providersIds as $providerId => $tags) {
            $provider = new Reference($providerId);

            $fakerDefinition->addMethodCall('addProvider', [$provider]);
            $providers[] = $provider;
        }

        $providerChainDefinition->addArgument($providers);
    }
}
