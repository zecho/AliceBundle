<?php

namespace Hautelook\AliceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class ProcessorCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('hautelook_alice.processor_chain');

        $taggedServices = $container->findTaggedServiceIds('hautelook_alice.processor');
        foreach ($taggedServices as $serviceId => $tags) {
            $definition->addMethodCall('addProcessor', [new Reference($serviceId)]);
        }
    }
}
