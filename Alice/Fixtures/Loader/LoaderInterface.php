<?php

namespace Hautelook\AliceBundle\Alice\Fixtures\Loader;

use Nelmio\Alice\Instances\Processor\Methods\MethodInterface;
use Nelmio\Alice\PersisterInterface;

/**
 * Interface LoaderInterface.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface LoaderInterface
{
    /**
     * Loads a fixture file
     *
     * @param string|array $dataOrFilename data array or filename
     *
     * @return array
     */
    public function load($dataOrFilename);

    /**
     * public interface to set the Persister interface
     *
     * @param PersisterInterface $manager
     */
    public function setPersister(PersisterInterface $manager);

    /**
     * adds a processor for processing extensions
     *
     * @param MethodInterface $processor
     */
    public function addProcessor(MethodInterface $processor);

    /**
     * @param object|array $provider Provider or array of providers
     */
    public function addProvider($provider);

    /**
     * Returns all references created by the loader
     *
     * @return array[object]
     */
    public function getReferences();
}
