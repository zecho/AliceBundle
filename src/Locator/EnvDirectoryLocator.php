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
use Nelmio\Alice\IsAServiceTrait;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class EnvDirectoryLocator implements FixtureLocatorInterface
{
    use IsAServiceTrait;

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
            //// ---$fixtureFiles = $fixtureFiles + $this->locateBundleFiles($bundle, $environment);---
            // do not use "plus" operator:
            //    "... for keys that exist in both arrays, the elements from the left-hand array will be used,
            //    and the matching elements from the right-hand array will be IGNORED."
            //
            //        $bundle1Files = ['bundle1/001-A.php', 'bundle1/001-B.php', 'bundle1/001-C.php'];
            //        $bundle2Files = ['bundle2/001-A.php', 'bundle2/001-B.php', 'bundle2/001-C.php', 'bundle2/001-D.php'];
            //        ----------
            //        var_dump($bundle1Files + $bundle2Files);
            //        > 4 elements: 3 from bundle1 + 1 from bundle2
            //
            //        var_dump(array_merge($bundle1Files, $bundle2Files));
            //        > 7 elements: 3 from bundle1 + 4 from bundle2
            $fixtureFiles = array_merge($fixtureFiles, $this->locateBundleFiles($bundle, $environment));
        }

        return $fixtureFiles;
    }

    private function locateBundleFiles(BundleInterface $bundle, string $environment): array
    {
        $path = '' !== $environment
            ? sprintf('%s/%s/%s', $bundle->getPath(), $this->fixturesPath, $environment)
            : sprintf('%s/%s', $bundle->getPath(), $this->fixturesPath)
        ;
        $path = realpath($path);
        if (false === $path || false === file_exists($path)) {
            return [];
        }

        $files = SymfonyFinder::create()->files()->in($path)->depth(0)->name('/.*\.(ya?ml|php)$/i');

        // this sort helps to set an order with filename ( "001-root-level-fixtures.yml", "002-another-level-fixtures.yml", ... )
        $files = $files->sort( function ($a, $b) { return strcasecmp($a, $b); } );

        $fixtureFiles = [];
        foreach ($files as $file) {
            $fixtureFiles[$file->getRealPath()] = true;
        }

        return array_keys($fixtureFiles);
    }
}
