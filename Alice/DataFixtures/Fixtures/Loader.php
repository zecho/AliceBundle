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
     * @return PersisterInterface
     */
    public function getPersister()
    {
        return $this->manager;
    }
}
