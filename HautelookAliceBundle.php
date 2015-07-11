<?php

namespace Hautelook\AliceBundle;

use Hautelook\AliceBundle\DependencyInjection\Compiler\ProcessorCompilerPass;
use Hautelook\AliceBundle\DependencyInjection\Compiler\ProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HautelookAliceBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ProcessorCompilerPass());
        $container->addCompilerPass(new ProviderCompilerPass());
    }
}
