<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Doctrine\DataFixtures\Executor;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor as DoctrineORMExecutor;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Hautelook\AliceBundle\Alice\DataFixtures\LoaderInterface;
use Nelmio\Alice\Persister\Doctrine;

/**
 * Class responsible for executing data fixtures.
 *
 * @author Jonathan H. Wage <jonwage@gmail.com>
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class ORMExecutor extends DoctrineORMExecutor implements ExecutorInterface
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
        $this->getObjectManager()->transactional(function (EntityManagerInterface $manager) use ($executor, $fixtures, $append) {
            if (false === $append) {
                $executor->purge();
            }
            $this->loader->load(new Doctrine($manager), $fixtures);
        });
    }
}
