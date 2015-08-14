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
