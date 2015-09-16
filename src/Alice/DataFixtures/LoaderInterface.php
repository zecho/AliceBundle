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
use Nelmio\Alice\ProcessorInterface;

/**
 * The loader is class responsible for loading the fixtures files into objects and persist them into the database.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface LoaderInterface
{
    /**
     * Loads the fixtures files.
     *
     * @param PersisterInterface $persister     Class used to persist fixtures.
     * @param string[]           $fixturesFiles Path to the fixtures files to loads.
     *
     * @return \object[] Persisted objects
     */
    public function load(PersisterInterface $persister, array $fixturesFiles);

    /**
     * @return ProcessorInterface[]
     */
    public function getProcessors();

    /**
     * @return bool If true only persist once the objects loaded.
     */
    public function getPersistOnce();

    /**
     * @return int Maximum number of time the loader will try to load the files passed
     */
    public function getLoadingLimit();
}
