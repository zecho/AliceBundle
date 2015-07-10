<?php

namespace Hautelook\AliceBundle\Alice\Fixtures\Loader;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class LoaderChain
{
    /**
     * @var LoaderInterface[]
     */
    private $loaders = [];

    /**
     * @param string          $loaderId
     * @param LoaderInterface $loader
     */
    public function addLoader($loaderId, LoaderInterface $loader)
    {
        $this->loaders[$loaderId] = $loader;
    }

    /**
     * @return LoaderInterface[]
     */
    public function getLoaders()
    {
        return $this->loaders;
    }

    /**
     * @param string $id Loader ID
     *
     * @return LoaderInterface
     */
    public function getLoader($id)
    {
        return $this->loaders[$id];
    }
}
