<?php

namespace Hautelook\AliceBundle\Faker\Provider;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class ProviderChain
{
    /**
     * @var array
     */
    private $providers = [];

    /**
     * @param $provider
     */
    public function addProvider($provider)
    {
        $this->providers[] = $provider;
    }

    /**
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }
}
