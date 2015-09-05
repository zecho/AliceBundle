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

use Doctrine\Common\DataFixtures\Executor\PHPCRExecutor as DoctrinePHPCRExecutor;
use Doctrine\Common\DataFixtures\Purger\PHPCRPurger;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\PHPCR\DocumentManager;
use Hautelook\AliceBundle\Alice\DataFixtures\LoaderInterface;
use Nelmio\Alice\Persister\Doctrine;

/**
 * Class responsible for executing data fixtures.
 *
 * @author Jonathan H. Wage <jonwage@gmail.com>
 * @author Daniel Barsotti <daniel.barsotti@liip.ch>
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class PHPCRExecutor extends DoctrinePHPCRExecutor implements ExecutorInterface
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
     */
    public function __construct(DocumentManager $manager, LoaderInterface $loader, PHPCRPurger $purger = null)
    {
        parent::__construct($manager, $purger);

        $this->loader = $loader;
    }

    /** @inheritDoc */
    public function execute(array $fixtures, $append = false)
    {
        $this->executeExecutor($this, $this->getObjectManager(), $this->loader, $fixtures, $append);
    }
}

