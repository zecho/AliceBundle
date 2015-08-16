<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Doctrine\Finder;

use Doctrine\Common\DataFixtures\Loader;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Extends its parent class to take into account doctrine data loaders.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class Finder extends \Hautelook\AliceBundle\Finder\Finder
{
    /**
     * Get the fixtures path for a given directory. It is recommended not to take into account sub directories as
     * this function will be called for them later on.
     *
     * @param SymfonyFinder $finder
     * @param string        $path Directory path
     *
     * @return string[]
     */
    protected function getFixturesFromDirectory(SymfonyFinder $finder, $path)
    {
        $fixtures = [];

        $loader = new Loader();
        $loader->loadFromDirectory($path);

        // $_loader has either found a data loader or has no fixtures
        // If a data loader is found, takes the data loader fixtures
        // Otherwise takes all fixtures
        if (0 !== count($loader->getFixtures())) {
            foreach ($loader->getFixtures() as $_loader) {
                /** @var Loader $_loader */
                $fixtures = array_merge($fixtures, $_loader->getFixtures());
            }
        } else {
            $finder->in($path)->files()->name('*.yml');
            foreach ($finder as $file) {
                /** @var SplFileInfo $file */
                $fixtures[] = $file->getRealPath();
            }
        }

        return $fixtures;
    }
}
