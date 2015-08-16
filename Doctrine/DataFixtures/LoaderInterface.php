<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Doctrine\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface as DoctrineFixtureInterface;

/**
 * Interface LoaderInterface.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface LoaderInterface extends DoctrineFixtureInterface
{
    /**
     * Returns an array of file paths to fixtures.
     *
     * @return string[]
     */
    public function getFixtures();
}
