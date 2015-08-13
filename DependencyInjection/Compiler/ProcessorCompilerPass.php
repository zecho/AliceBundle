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
        $processorChainDefinition = $container->findDefinition('hautelook_alice.alice.processor_chain');

        $processorsIds = $container->findTaggedServiceIds('hautelook_alice.alice.processor');
        $processors = [];
        foreach ($processorsIds as $processorId => $tags) {
            $processors[] = new Reference($processorId);
        }

        $processorChainDefinition->addArgument($processors);
    }
}
