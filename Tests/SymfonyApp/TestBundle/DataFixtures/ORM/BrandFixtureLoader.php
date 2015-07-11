<?php

namespace Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Hautelook\AliceBundle\Doctrine\DataFixtures\AbstractDataFixtureLoader;

class BrandFixtureLoader extends AbstractDataFixtureLoader
{
    /**
     * {@inheritdoc}
     */
    protected function getFixtures()
    {
        return array(
            __DIR__.'/brand.yml',
            __DIR__.'/product.yml',
        );
    }
}
