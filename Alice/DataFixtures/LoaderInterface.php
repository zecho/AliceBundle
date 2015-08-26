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

    /**
     * @return array Options of Alice fixtures loader. Has the following keys:
     *               - providers (array): Faker data providers
     *               - locale (string): Faker locale used to select the data providers to use
     *               - seed (int): seed used for the generation of random data by Faker
     *               - persist_once (bool): option of Alice loader
     *               - logger (\Psr\Log\LoggerInterface): logger used by Alice loader
     */
    public function getOptions();

    /**
     * @return array|ProcessorInterface[]
     */
    public function getProcessors();
}
