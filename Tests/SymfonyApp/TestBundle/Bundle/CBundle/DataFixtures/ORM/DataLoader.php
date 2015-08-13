<?php

namespace Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Bundle\CBundle\DataFixtures\ORM;

use Hautelook\AliceBundle\Doctrine\DataFixtures\AbstractLoader;

class DataLoader extends AbstractLoader
{
    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return array(
            __DIR__.'/../../../ABundle/DataFixtures/ORM/aentity.yml',
            __DIR__.'/../../../BBundle/DataFixtures/ORM/bentity.yml',
        );
    }
}
