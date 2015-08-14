<?php

namespace Hautelook\AliceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
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
