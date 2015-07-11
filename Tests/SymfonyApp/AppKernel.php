<?php

namespace Hautelook\AliceBundle\Tests\SymfonyApp;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use Fidry\PsyshBundle\PsyshBundle;
use Hautelook\AliceBundle\HautelookAliceBundle;
use Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\TestBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return [
            new DoctrineBundle(),
            new DoctrineFixturesBundle(),
            new FrameworkBundle(),
            new HautelookAliceBundle(),
            new TestBundle(),
            new PsyshBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.yml');
    }
}
