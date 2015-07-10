<?php

namespace Hautelook\AliceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class ChainCompilerPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private $definitionId;

    /**
     * @var string
     */
    private $taggedServiceIdsId;

    /**
     * @var string
     */
    private $methodCallName;

    /**
     * @param string $definitionId
     * @param string $taggedServiceIdsId
     * @param string $methodCallName
     */
    public function __construct($definitionId, $taggedServiceIdsId, $methodCallName)
    {
        $this->definitionId = $definitionId;
        $this->taggedServiceIdsId = $taggedServiceIdsId;
        $this->methodCallName = $methodCallName;
    }


    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition($this->definitionId);

        $taggedServices = $container->findTaggedServiceIds($this->taggedServiceIdsId);
        foreach ($taggedServices as $serviceId => $tags) {
            $definition->addMethodCall($this->methodCallName, [new Reference($serviceId)]);
        }
    }
}
