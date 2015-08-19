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
    const BUNDLE_FIXTURES_PATH = 'DataFixtures/ORM';

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
     * Gets all fixtures files path.
     *
     * For first get all the path for where to look for fixtures.
     * For each path, will try to get fixtures from data loaders. If no data loader is found, will take all the
     * fixtures.
     *
     * @param KernelInterface   $kernel
     * @param BundleInterface[] $bundles
     * @param string            $environment
     *
     * @return string[] Fixtures files real paths.
     */
    public function getFixtures(KernelInterface $kernel, array $bundles, $environment)
    {
        $loadersPaths = $this->getLoadersPaths($bundles, $environment);

        // Add all fixtures to the new Doctrine loader
        $fixtures = [];
        foreach ($loadersPaths as $path) {
            if (false === is_dir($path)) {
                throw new \InvalidArgumentException(sprintf('Expected "%s" to be a directory.', $path));
            }

            $fixtures = array_merge($fixtures, $this->getFixturesFromDirectory($path));
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
     * @param KernelInterface         $kernel
     * @param string[]|\SplFileInfo[] $fixtures
     *
     * @return string[] Fixtures real path
     * @throws \InvalidArgumentException File not found.
     */
    protected function resolveFixtures(KernelInterface $kernel, array $fixtures)
    {
        $resolvedFixtures = [];

        // Get real fixtures path
        foreach ($fixtures as $index => $fixture) {
            if ($fixture instanceof \SplFileInfo) {
                $filePath = $fixture->getRealPath();

                if (false === $filePath) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'The file %s pointed by a %s instance was not found.',
                            (string)$fixture,
                            get_class($fixture)
                        )
                    );
                }
                $resolvedFixtures[$filePath] = true;

                continue;
            }

            if (false === is_string($fixture)) {
                throw new \InvalidArgumentException(
                    'Expected fixtures passed to be either strings or a SplFileInfo instances.'
                );
            }

            if ('@' === $fixture[0]) {
                // If $kernel fails to resolve the resource, will throw a \InvalidArgumentException exception
                $resolvedFixtures[$kernel->locateResource($fixture, null, true)] = true;

                continue;
            }

            $realPath = realpath($fixture);
            if (false === $realPath || false === file_exists($realPath)) {
                throw new \InvalidArgumentException(sprintf('The file "%s" was not found', $fixture));
            }
            $resolvedFixtures[$realPath] = true;
        }

        return array_keys($resolvedFixtures);
    }

    /**
     * Get the fixtures path for a given directory. It is recommended not to take into account sub directories as
     * this function will be called for them later on.
     *
     * @param string $path Directory path
     *
     * @return string[]|SplFileInfo[] Fixtures paths
     */
    protected function getFixturesFromDirectory($path)
    {
        $fixtures = [];

        $finder = SymfonyFinder::create()->in($path)->depth(0)->files()->name('*.yml');
        foreach ($finder as $file) {
            /** @var SplFileInfo $file */
            $fixtures[$file->getRealPath()] = true;
        }

        return array_keys($fixtures);
    }

    /**
     * Gets paths to directories containing loaders and fixtures files.
     *
     * @param BundleInterface[] $bundles
     * @param string            $environment
     *
     * @return string[] Real paths to loaders.
     */
    protected function getLoadersPaths(array $bundles, $environment)
    {
        $environments = [
            lcfirst($environment) => true,
            ucfirst($environment) => true,
        ];

        $paths = [];
        foreach ($bundles as $bundle) {
            $path = sprintf('%s/%s', $bundle->getPath(), self::BUNDLE_FIXTURES_PATH);
            if (true === file_exists($path)) {
                $paths[$path] = true;
                try {
                    $files = SymfonyFinder::create()->directories()->in($path);
                    foreach ($files as $file) {
                        /** @var SplFileInfo $file */
                        if (true === isset($environments[$file->getRelativePathname()])) {
                            $paths[$file->getRealPath()] = true;
                        }
                    }
                } catch (\InvalidArgumentException $exception) {
                }
            }
        }
        
        return array_keys($paths);
    }
}
