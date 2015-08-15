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
            '@TestABundle/DataFixtures/ORM/aentity.yml',
            '@TestBBundle/DataFixtures/ORM/bentity.yml',
        );
    }
}
