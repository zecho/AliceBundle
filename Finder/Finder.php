<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Finder;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class responsible retrieving the fixtures path to load.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class Finder
{
    /**
     * Looks at all the bundles registered in the application to return the bundles requested. An exception is thrown
     * if a bundle has not been found.
     *
     * @param Application $application Application in which bundles will be looked in.
     * @param string[]    $names       Bundle names.
     *
     * @return BundleInterface[] Bundles requested.
     * @throws \RuntimeException A bundle could not be resolved.
     */
    public function resolveBundles(Application $application, array $names)
    {
        $bundles = $application->getKernel()->getBundles();

        $result = [];
        foreach ($names as $name) {
            if (false === isset($bundles[$name])) {
                throw new \RuntimeException(sprintf(
                    'The bundle "%s" was not found. Bundles availables are: %s.',
                    $name,
                    implode('", "', array_keys($bundles))
                ));
            }

            $result[$name] = $bundles[$name];
        }

        return $result;
    }

    /**
     * Gets all fixtures.
     *
     * For first get all the path for where to look for fixtures.
     * For each path, will try to get fixtures from data loaders. If no data loader is found, will take all the
     * fixtures.
     *
     * @param KernelInterface   $kernel
     * @param BundleInterface[] $bundles
     * @param array             $environment
     *
     * @return string[]
     */
    public function getFixtures(KernelInterface $kernel, array $bundles, $environment)
    {
        $finder = SymfonyFinder::create();
        $loadersPaths = $this->getLoadersPaths($bundles, $environment);

        // Add all fixtures to the new Doctrine loader
        $fixtures = [];
        foreach ($loadersPaths as $path) {
            if (false === is_dir($path)) {
                throw new \InvalidArgumentException(sprintf('Expected "%s" to be a directory.', $path));
            }

            $fixtures= array_merge($fixtures, $this->getFixturesFromDirectory($finder, $path));
        }

        if (0 === count($fixtures)) {
            throw new \InvalidArgumentException(
                sprintf('Could not find any fixtures to load in: %s', "\n\n- ".implode("\n- ", $loadersPaths))
            );
        }

        // Get real fixtures path
        return $this->resolveFixtures($kernel, $fixtures);
    }

    /**
     * Gets the real path of each fixtures.
     *
     * @param KernelInterface $kernel
     * @param array           $fixtures
     *
     * @return array
     * @throws \InvalidArgumentException File not found.
     */
    protected function resolveFixtures(KernelInterface $kernel, array $fixtures)
    {
        // Get real fixtures path
        foreach ($fixtures as $index => $fixture) {
            if ('@' === $fixture[0]) {
                $fixtures[$index] = $kernel->locateResource($fixture);
            } else {
                $realPath = realpath($fixture);
                if (false === $realPath || false === file_exists($realPath)) {
                    throw new \InvalidArgumentException(sprintf('The file "%s" was not found', $fixture));
                }
                $fixtures[$index] = realpath($fixture);
            }
        }

        return array_unique($fixtures);
    }

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

        $finder->in($path)->files()->name('*.yml');
        foreach ($finder as $file) {
            /** @var SplFileInfo $file */
            $fixtures[] = $file->getRealPath();
        }

        return $fixtures;
    }

    /**
     * Gets paths to loaders.
     *
     * @param BundleInterface[] $bundles
     * @param string            $environment
     *
     * @return string[] Real paths to loaders.
     */
    private function getLoadersPaths(array $bundles, $environment)
    {
        $finder = SymfonyFinder::create();

        $paths = [];
        foreach ($bundles as $bundle) {
            $path = $bundle->getPath().'/DataFixtures/ORM';
            if (true === file_exists($path)) {
                $paths[] = $path;
                try {
                    $files = $finder->directories()->in($path);
                    foreach ($files as $file) {
                        /** @var SplFileInfo $file */
                        if ($environment === $file->getRelativePathname()) {
                            $paths[] = $file->getRealPath();
                        }
                    }
                } catch (\InvalidArgumentException $exception) {
                }
            }
        }

        return array_unique($paths);
    }
}
