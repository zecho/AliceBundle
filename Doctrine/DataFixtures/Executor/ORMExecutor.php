<?php

namespace Hautelook\AliceBundle\Doctrine\DataFixtures\Executor;

use Doctrine\Common\DataFixtures\Executor\AbstractExecutor;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Event\Listener\ORMReferenceListener;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Hautelook\AliceBundle\Alice\DataFixtures\LoaderInterface;

/**
 * Class responsible for executing data fixtures.
 *
 * @author Jonathan H. Wage <jonwage@gmail.com>
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class ORMExecutor extends AbstractExecutor
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * Construct new fixtures loader instance.
     *
     * @param EntityManagerInterface $manager EntityManagerInterface instance used for persistence.
     * @param ORMPurger              $purger
     */
    public function __construct(EntityManagerInterface $manager, LoaderInterface $loader, ORMPurger $purger = null)
    {
        $this->em = $manager;
        if (null !== $purger) {
            $this->purger = $purger;
            $this->purger->setEntityManager($manager);
        }

        parent::__construct($manager);

        $this->listener = new ORMReferenceListener($this->referenceRepository);
        $manager->getEventManager()->addEventSubscriber($this->listener);
        $this->loader = $loader;
    }

    /**
     * Retrieve the EntityManagerInterface instance this executor instance is using.
     *
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    public function getObjectManager()
    {
        return $this->em;
    }

    /** @inheritDoc */
    public function setReferenceRepository(ReferenceRepository $referenceRepository)
    {
        $this->em->getEventManager()->removeEventListener(
            $this->listener->getSubscribedEvents(),
            $this->listener
        );

        $this->referenceRepository = $referenceRepository;
        $this->listener = new ORMReferenceListener($this->referenceRepository);
        $this->em->getEventManager()->addEventSubscriber($this->listener);
    }

    /** @inheritDoc */
    public function execute(array $fixtures, $append = false)
    {
        $executor = $this;
        $this->em->transactional(function (EntityManagerInterface $manager) use ($executor, $fixtures, $append) {
            if ($append === false) {
                $executor->purge();
            }
            $this->loader->load($manager, $fixtures);
        });
    }
}
