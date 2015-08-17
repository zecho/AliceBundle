<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Faker\Provider;

/**
 * Calls multiple Faker providers instances in a chain.
 *
 * This class accepts multiple instances of Faker providers to be passed to the constructor.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class ProviderChain
{
    /**
     * @var array
     */
    private $providers;

    /**
     * @param array $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }
}
