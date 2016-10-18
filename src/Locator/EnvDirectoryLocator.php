<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Locator;

use Hautelook\AliceBundle\FixtureLocatorInterface;
use Nelmio\Alice\NotClonableTrait;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class EnvDirectoryLocator implements FixtureLocatorInterface
{
    use NotClonableTrait;

    /**
     * @var string
     */
    private $fixturesPath;

    /**
     * @param string $fixturePath Path to which to look for fixtures relative to the bundle path.
     */
    public function __construct(string $fixturePath)
    {
        $this->fixturesPath = $fixturePath;
    }

    /**
     * Locate fixture files found inside a folder matching the environment name.
     *
     * For example, if the given fixture path is 'Resources/fixtures', it will try to locate
     * the files in the 'Resources/fixtures/dev' for each bundle passed ('dev' being the
     * environment).
     *
     * {@inheritdoc}
     */
    public function locateFiles(array $bundles, string $environment): array
    {
        $fixtureFiles = [];
        foreach ($bundles as $bundle) {
            $fixtureFiles = $fixtureFiles + $this->locateBundleFiles($bundle, $environment);
        }

        return $fixtureFiles;
    }

    private function locateBundleFiles(BundleInterface $bundle, string $environment): array
    {
        $path = sprintf('%s/%s/%s', $bundle->getPath(), $this->fixturesPath, $environment);
        if (false === file_exists($path)) {
            return [];
        }

        $files = SymfonyFinder::create()->files()->in($path)->name('/.*\.(ya?ml|php)$/i');
        $fixtureFiles = [];
        foreach ($files as $file) {
            $fixtureFiles[$file->getRealPath()] = true;
        }

        return array_keys($fixtureFiles);
    }
}
