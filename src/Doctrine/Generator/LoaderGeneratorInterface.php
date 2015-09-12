<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Doctrine\Generator;

use Hautelook\AliceBundle\Alice\DataFixtures\Fixtures\LoaderInterface as FixturesLoaderInterface;
use Hautelook\AliceBundle\Alice\DataFixtures\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Class responsible to generate the loader to use for loading the fixtures and persisting them into the database.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface LoaderGeneratorInterface
{
    /**
     * Create a new loader using Doctrine data loaders as providers.
     *
     * @param LoaderInterface         $loader
     * @param FixturesLoaderInterface $fixturesLoader
     * @param BundleInterface[]       $bundles
     * @param string                  $environment
     *
     * @return LoaderInterface
     */
    public function generate(
        LoaderInterface $loader,
        FixturesLoaderInterface $fixturesLoader,
        array $bundles,
        $environment
    );
}
