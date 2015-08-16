<?php

namespace Hautelook\AliceBundle\Doctrine\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * @author Baldur Rensch <brensch@gmail.com>
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
abstract class AbstractLoader extends ContainerAware implements LoaderInterface
{
    /**
     * Loads the fixtures files.
     *
     * @param ObjectManager $objectManager
     *
     * @return \object[] Persisted objects
     */
    public function load(ObjectManager $objectManager)
    {
        return $this->container->get('hautelook_alice.fixtures.loader')->load($objectManager, $this->getFixtures());
    }
}
