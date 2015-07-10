<?php

namespace Hautelook\AliceBundle\Doctrine\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Hautelook\AliceBundle\Alice\AbstractDataFixtureLoader as HautelookAliceDataFixtureLoader;
use Nelmio\Alice\Persister\Doctrine;

/**
 * @author Baldur Rensch <brensch@gmail.com>
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
abstract class AbstractDataFixtureLoader extends HautelookAliceDataFixtureLoader implements FixtureInterface
{
    /**
     * Loads the fixtures files
     *
     * @param ObjectManager $objectManager
     */
    public function load(ObjectManager $objectManager)
    {
        parent::loadFixtures($objectManager);
    }
}
