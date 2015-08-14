<?php

namespace Hautelook\AliceBundle\Doctrine\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Hautelook\AliceBundle\Alice\DataFixtures\AbstractLoader as HautelookAliceDataFixtureLoader;

/**
 * @author Baldur Rensch <brensch@gmail.com>
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
abstract class AbstractLoader extends HautelookAliceDataFixtureLoader implements FixtureInterface
{
    /**
     * Loads the fixtures files.
     *
     * @param ObjectManager $objectManager
     */
    public function load(ObjectManager $objectManager)
    {
        parent::loadFixtures($objectManager);
    }
}
