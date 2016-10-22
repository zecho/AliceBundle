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
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Extends its parent class to take into account doctrine data loaders.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class FixturesFinder extends \Hautelook\AliceBundle\Finder\FixturesFinder implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface|null
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     *
     * Extended to look for data loaders. If a data loader is found, will take the fixtures from it instead of taking
     * all the fixtures files.
     */
    public function getFixturesFromDirectory($path)
    {
        $fixtures = [];

        $loaders = $this->getDataLoadersFromDirectory($path);
        foreach ($loaders as $loader) {
            $fixtures = array_merge($fixtures, $loader->getFixtures());
        }

        // If no data loader is found, takes all fixtures files
        if (0 === count($loaders)) {
            return parent::getFixturesFromDirectory($path);
        }

        return $fixtures;
    }

    /**
     * Gets all data loaders instances.
     *
     * For first get all the path for where to look for data loaders.
     *
     * @param BundleInterface[] $bundles
     * @param string            $environment
     *
     * @return LoaderInterface[] Fixtures files real paths.
     */
    public function getDataLoaders(array $bundles, $environment)
    {
        $loadersPaths = $this->getLoadersPaths($bundles, $environment);

        // Add all fixtures to the new Doctrine loader
        $loaders = [];
        foreach ($loadersPaths as $path) {
            if (is_dir($path)) {
                $loaders = array_merge($loaders, $this->getDataLoadersFromDirectory($path));
            }
        }

        return $loaders;
    }

    /**
     * Get data loaders inside the given directory.
     *
     * @param string $path Directory path
     *
     * @return LoaderInterface[]
     */
    private function getDataLoadersFromDirectory($path)
    {
        $includedFilesFromPath = $this->includePhpFiles($path, array_flip(get_included_files()));

        return $this->loadDataLoaders(
            get_declared_classes(),
            $includedFilesFromPath
        );
    }

    /**
     * Includes all the PHP files present in a folder and returns the list of the included files.
     *
     * @param string $path
     * @param array  $includedFiles Real files path as keys
     *
     * @return array Loaded real files path as keys
     */
    private function includePhpFiles($path, array $includedFiles)
    {
        $includedFilesFromPath = [];
        $finder = SymfonyFinder::create()->depth(0)->in($path)->files()->name('*.php');
        foreach ($finder as $file) {
            /* @var SplFileInfo $file */
            $includedFilesFromPath[$fileRealPath = $file->getRealPath()] = true;

            if (false === array_key_exists($fileRealPath, $includedFiles)) {
                require_once $fileRealPath;
            }
        }

        return array_fill_keys(array_keys($includedFilesFromPath), true);
    }

    /**
     * Looks for loaders among the classes given.
     *
     * @param array $classNames
     * @param array $includedFilesFromPath Real files path as keys
     *
     * @return LoaderInterface[]
     */
    private function loadDataLoaders(array $classNames, array $includedFilesFromPath)
    {
        $loaders = [];
        $loaderInterface = LoaderInterface::class;

        foreach ($classNames as $className) {
            $reflectionClass = new \ReflectionClass($className);
            $sourceFile = $reflectionClass->getFileName();

            if (false === isset($includedFilesFromPath[$sourceFile])) {
                // The class does not come from the loaded directories
                continue;
            }

            if ($reflectionClass->implementsInterface($loaderInterface) && false === $reflectionClass->isAbstract()) {
                $loader = new $className();
                $loaders[$className] = $loader;

                if ($loader instanceof ContainerAwareInterface) {
                    $loader->setContainer($this->container);
                }
            }
        }

        return $loaders;
    }
}
