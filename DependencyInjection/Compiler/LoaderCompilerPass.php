<?php

namespace Hautelook\AliceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class LoaderCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('hautelook_alice.fixtures.loader_chain');

        $taggedServices = $container->findTaggedServiceIds('hautelook_alice.fixtures.loader');
        foreach ($taggedServices as $serviceId => $tags) {
            $definition->addMethodCall('addLoader', [$serviceId, new Reference($serviceId)]);
        }
    }
}
