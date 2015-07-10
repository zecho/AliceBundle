<?php

namespace Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Hautelook\AliceBundle\Doctrine\DataFixtures\AbstractDataFixtureLoader;

class BrandFixtureLoader extends AbstractDataFixtureLoader implements FixtureInterface
{
    /**
     * Returns an array of file paths to fixtures
     *
     * @return string[]
     */
    protected function getFixtures()
    {
        return array(
            __DIR__ . '/brand.yml',
        );
    }
}
