<?php

namespace Hautelook\AliceBundle\Tests\Functional;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Hautelook\AliceBundle\HautelookAliceBundle(),

            new \Hautelook\AliceBundle\Tests\Functional\TestBundle\TestBundle(),
        );
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir().'/AliceBundle/';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
