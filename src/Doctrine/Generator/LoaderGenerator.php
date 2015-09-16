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
use Hautelook\AliceBundle\Alice\DataFixtures\Loader;
use Hautelook\AliceBundle\Alice\DataFixtures\LoaderInterface;
use Hautelook\AliceBundle\Doctrine\Finder\FixturesFinder;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class LoaderGenerator implements LoaderGeneratorInterface
{
    /**
     * @var FixturesFinder
     */
    private $fixturesFinder;

    /**
     * @param FixturesFinder $fixturesFinder
     */
    public function __construct(FixturesFinder $fixturesFinder)
    {
        $this->fixturesFinder = $fixturesFinder;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(
        LoaderInterface $loader,
        FixturesLoaderInterface $fixturesLoader,
        array $bundles,
        $environment
    ) {
        if (!$loader instanceof Loader) {
            throw new \UnexpectedValueException('Unsupported loader for this generator. Must be an instance of Hautelook\AliceBundle\Alice\DataFixtures\Loader.');
        }

        $doctrineDataLoaders = $this->fixturesFinder->getDataLoaders($bundles, $environment);

        $_fixturesLoader = clone $fixturesLoader;
        $_fixturesLoader->addProvider($doctrineDataLoaders);

        return new Loader(
            $_fixturesLoader,
            $loader->getProcessorChain(),
            $loader->getPersistOnce(),
            $loader->getLoadingLimit()
        );
    }
}
