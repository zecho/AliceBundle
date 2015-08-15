<?php

namespace Hautelook\AliceBundle\Doctrine\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface as DoctrineFixtureInterface;

/**
 * Interface FixtureInterface.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface FixtureInterface extends DoctrineFixtureInterface
{
    /**
     * Returns an array of file paths to fixtures.
     *
     * @return string[]
     */
    public function getFixtures();
}
