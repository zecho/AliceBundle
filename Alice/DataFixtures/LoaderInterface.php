<?php

namespace Hautelook\AliceBundle\Alice\DataFixtures;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface LoaderInterface
{
    /**
     * Loads the fixtures files.
     *
     * @param object $persister
     * @param array  $fixtures
     *
     * @return \object[] Persisted objects
     */
    public function load($persister, array $fixtures);
}
