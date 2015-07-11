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
        $definitions = [];
        $definitions[] = $container->findDefinition('hautelook_alice.faker');
        $definitions[] = $container->findDefinition('hautelook_alice.faker.provider_chain');

        $taggedServices = $container->findTaggedServiceIds('hautelook_alice.faker.provider');
        foreach ($taggedServices as $serviceId => $tags) {
            foreach ($definitions as $definition) {
                $definition->addMethodCall('addProvider', [new Reference($serviceId)]);
            }
        }
    }
}
