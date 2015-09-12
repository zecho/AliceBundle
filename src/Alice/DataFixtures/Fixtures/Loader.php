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

use Nelmio\Alice\PersisterInterface;

/**
 * Bridge for Alice's loader.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class Loader extends \Nelmio\Alice\Fixtures\Loader implements LoaderInterface
{
    /**
     * Loads a fixture file.
     *
     * @param string|array $dataOrFilename May either be the path to the file in which case it will be parsed, or be
     *                                     an array of data (then skips the parsing). The format of the array of data
     *                                     depends of the builders used.
     * @param array        $references     Array where the key is the object name and the value the actual object
     *                                     class. The references are used to inject objects which the loader should be
     *                                     aware of while loading the file.
     *
     * @return array|\object[]
     */
    public function load($dataOrFilename, array $references = [])
    {
        $this->setReferences($references);
        $objects = parent::load($dataOrFilename);
        $this->setReferences([]);

        return $objects;
    }

    /**
     * @return PersisterInterface
     */
    public function getPersister()
    {
        return $this->manager;
    }
}
