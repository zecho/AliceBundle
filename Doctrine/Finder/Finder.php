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

use Hautelook\AliceBundle\Doctrine\DataFixtures\LoaderInterface;
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
     * {@inheritdoc}
     *
     * Extended to look for data loaders. If a data loader is found, will take the fixtures from it instead of taking
     * all the fixtures files.
     */
    protected function getFixturesFromDirectory($path)
    {
        $fixtures = [];

        $loaders = $this->getDataLoaders($path);
        foreach ($loaders as $loader) {
            $fixtures = array_merge($fixtures, $loader->getFixtures());
        }
        
        // If no data loader is found, takes all fixtures files
        if (0 === count($fixtures)) {

            return parent::getFixturesFromDirectory($path);
        }

        return $fixtures;
    }

    /**
     * Get data loaders inside the given directory.
     *
     * @param string        $path Directory path
     *
     * @return LoaderInterface[]
     */
    private function getDataLoaders($path)
    {
        $loaders = [];

        // Get all PHP classes in given folder
        $phpClasses = [];
        $finder = SymfonyFinder::create()->depth(0)->in($path)->files()->name('*.php');
        foreach ($finder as $file) {
            /** @var SplFileInfo $file */
            $phpClasses[$file->getRealPath()] = true;
            require_once $file->getRealPath();
        }

        // Check if PHP classes are data loaders or not
        foreach (get_declared_classes() as $className) {
            $reflectionClass = new \ReflectionClass($className);
            $sourceFile = $reflectionClass->getFileName();

            if (true === isset($phpClasses[$sourceFile])) {
                if ($reflectionClass->implementsInterface('Hautelook\AliceBundle\Doctrine\DataFixtures\LoaderInterface')) {
                    $loaders[] = new $className;
                }
            }
        }

        return $loaders;
    }
}
