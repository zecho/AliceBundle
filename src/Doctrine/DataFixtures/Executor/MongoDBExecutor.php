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

use Doctrine\Common\DataFixtures\Executor\MongoDBExecutor as DoctrineMongoDBExecutor;
use Doctrine\Common\DataFixtures\Purger\MongoDBPurger;
use Doctrine\ODM\MongoDB\DocumentManager;
use Hautelook\AliceBundle\Alice\DataFixtures\LoaderInterface;

/**
 * Class responsible for executing data fixtures.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class MongoDBExecutor extends DoctrineMongoDBExecutor implements ExecutorInterface
{
    use ExecutorTrait;

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * Construct new fixtures loader instance.
     *
     * @param DocumentManager $manager DocumentManager instance used for persistence.
     * @param LoaderInterface $loader
     * @param MongoDBPurger   $purger
     */
    public function __construct(DocumentManager $manager, LoaderInterface $loader, MongoDBPurger $purger = null)
    {
        parent::__construct($manager, $purger);

        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $fixtures, $append = false)
    {
        $this->executeExecutor($this, $this->getObjectManager(), $this->loader, $fixtures, $append);
    }
}
