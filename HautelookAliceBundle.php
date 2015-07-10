<?php

namespace Hautelook\AliceBundle;

use Hautelook\AliceBundle\DependencyInjection\Compiler\ChainCompilerPass;
use Hautelook\AliceBundle\DependencyInjection\Compiler\LoaderCompilerPass;
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

        $container->addCompilerPass(new LoaderCompilerPass());
        $container->addCompilerPass(
            new ChainCompilerPass(
                'hautelook_alice.processor_chain',
                'hautelook_alice.processor',
                'addProcessor'
            )
        );
        $container->addCompilerPass(
            new ChainCompilerPass(
                'hautelook_alice.faker.provider_chain',
                'hautelook_alice.faker.provider',
                'addProvider'
            )
        );
    }
}
