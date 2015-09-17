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

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Interface ExecutorInterface.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface ExecutorInterface
{
    /**
     * Retrieve the ObjectManager instance this executor instance is using.
     *
     * @return ObjectManager
     */
    public function getObjectManager();

    /**
     * Purges the database before loading.
     */
    public function purge();

    /**
     * Executes the given array of data fixtures.
     *
     * @param object[] $fixtures Array of fixtures to execute.
     * @param bool     $append   Whether to append the data fixtures or purge the database before loading.
     */
    public function execute(array $fixtures, $append = false);

    /**
     * Sets the logger callable to execute with the log() method.
     *
     * @param callable $logger
     */
    public function setLogger($logger);
}
