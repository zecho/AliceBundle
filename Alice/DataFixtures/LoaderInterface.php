<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Alice\DataFixtures;

use Nelmio\Alice\PersisterInterface;

/**
 * The loader is class responsible for loading the fixtures.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface LoaderInterface
{
    /**
     * Loads the fixtures files.
     *
     * @param PersisterInterface $persister Class used to persist fixtures.
     * @param string[]           $fixtures  Path to the fixtures files to loads.
     *
     * @return \object[] Persisted objects
     */
    public function load($persister, array $fixtures);
}
