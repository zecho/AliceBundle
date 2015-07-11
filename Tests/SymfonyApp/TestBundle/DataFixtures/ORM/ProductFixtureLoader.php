<?php

namespace Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Hautelook\AliceBundle\Doctrine\DataFixtures\AbstractDataFixtureLoader;

class ProductFixtureLoader extends AbstractDataFixtureLoader implements DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getFixtures()
    {
        return [];
        //TODO: make DependentFixture works
        return array(
            __DIR__.'/product.yml',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return array(
            'Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\DataFixtures\ORM\BrandFixtureLoader',
        );
    }
}
