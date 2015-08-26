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

/**
 * Interface ExecutorInterface.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface ExecutorInterface
{
    /**
     * Executes the given array of data fixtures.
     *
     * @param array $fixtures Array of fixtures to execute.
     * @param boolean $append Whether to append the data fixtures or purge the database before loading.
     */
    public function execute(array $fixtures, $append = false);
}
