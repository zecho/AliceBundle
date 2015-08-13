<?php

namespace Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Bundle\CBundle\DataFixtures\ORM\BEnv;

use Hautelook\AliceBundle\Doctrine\DataFixtures\AbstractLoader;

class DataLoader extends AbstractLoader
{
    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return array(
            __DIR__.'/../../../../BBundle/DataFixtures/ORM/bentity.yml',
        );
    }
}
