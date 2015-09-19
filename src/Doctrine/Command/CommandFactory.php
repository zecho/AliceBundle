<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Doctrine\Command;

use Doctrine\Common\Persistence\ManagerRegistry;
use Hautelook\AliceBundle\Alice\DataFixtures\Fixtures\LoaderInterface as FixturesLoaderInterface;
use Hautelook\AliceBundle\Alice\DataFixtures\LoaderInterface;
use Hautelook\AliceBundle\Doctrine\DataFixtures\Executor\FixturesExecutorInterface;
use Hautelook\AliceBundle\Doctrine\Generator\LoaderGeneratorInterface;
use Hautelook\AliceBundle\Finder\FixturesFinderInterface;
use Hautelook\AliceBundle\Resolver\BundlesResolverInterface;

/**
 * Factory class to generate Doctrine load data fixtures commands.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class CommandFactory
{
    /**
     * @param string                    $name             Command name
     * @param ManagerRegistry           $doctrine
     * @param LoaderInterface           $loader
     * @param FixturesLoaderInterface   $fixturesLoader
     * @param FixturesFinderInterface   $fixturesFinder
     * @param BundlesResolverInterface  $bundlesResolver
     * @param LoaderGeneratorInterface  $loaderGenerator
     * @param FixturesExecutorInterface $fixturesExecutor
     *
     * @return LoadDataFixturesCommand
     */
    public function createCommand(
        $name,
        ManagerRegistry $doctrine,
        LoaderInterface $loader,
        FixturesLoaderInterface $fixturesLoader,
        FixturesFinderInterface $fixturesFinder,
        BundlesResolverInterface $bundlesResolver,
        LoaderGeneratorInterface $loaderGenerator,
        FixturesExecutorInterface $fixturesExecutor
    ) {
        return new LoadDataFixturesCommand(
            $name,
            $doctrine,
            $loader,
            $fixturesLoader,
            $fixturesFinder,
            $bundlesResolver,
            $loaderGenerator,
            $fixturesExecutor
        );
    }
}
