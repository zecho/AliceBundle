<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Alice\DataFixtures\Fixtures;

/**
 * Loader responsible for loading fixtures files into objects.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface LoaderInterface
{
    /**
     * Loads a fixture file to return the loaded objects.
     *
     * @param string|array $dataOrFilename data array or filename
     *
     * @return \object[] loaded objects
     */
    public function load($dataOrFilename);

    /**
     * Adds Faker providers.
     *
     * @param object|object[] $provider Provider or array of providers
     */
    public function addProvider($provider);
}
