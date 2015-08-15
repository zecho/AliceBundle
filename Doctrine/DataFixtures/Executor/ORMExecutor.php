<?php

namespace Hautelook\AliceBundle\Doctrine\DataFixtures\Executor;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor as DoctrineORMExecutor;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Hautelook\AliceBundle\Alice\DataFixtures\LoaderInterface;

/**
 * Class responsible for executing data fixtures.
 *
 * @author Jonathan H. Wage <jonwage@gmail.com>
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class ORMExecutor extends DoctrineORMExecutor
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * Construct new fixtures loader instance.
     *
     * @param EntityManagerInterface $manager EntityManagerInterface instance used for persistence.
     * @param LoaderInterface        $loader
     * @param ORMPurger              $purger
     */
    public function __construct(EntityManagerInterface $manager, LoaderInterface $loader, ORMPurger $purger = null)
    {
        parent::__construct($manager, $purger);

        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
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
