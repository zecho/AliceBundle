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
use Hautelook\AliceBundle\Alice\DataFixtures\LoaderInterface;
use Hautelook\AliceBundle\Finder\FixturesFinderInterface;
use Hautelook\AliceBundle\Resolver\BundlesResolverInterface;
use Hautelook\AliceBundle\Alice\DataFixtures\Fixtures\LoaderInterface as FixturesLoaderInterface;

/**
 * Factory class to generate Doctrine load data fixtures commands.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class CommandFactory
{
    /**
     * @param string                   $name Command name
     * @param ManagerRegistry          $doctrineRegistry
     * @param LoaderInterface          $loader
     * @param FixturesLoaderInterface  $fixturesLoader
     * @param FixturesFinderInterface  $fixturesFinder
     * @param BundlesResolverInterface $bundlesResolver
     *
     * @return LoadDataFixturesCommand
     */
    public function createCommand(
        $name,
        ManagerRegistry $doctrineRegistry,
        LoaderInterface $loader,
        FixturesLoaderInterface $fixturesLoader,
        FixturesFinderInterface $fixturesFinder,
        BundlesResolverInterface $bundlesResolver
    ) {
        return new LoadDataFixturesCommand(
            $name,
            $doctrineRegistry,
            $loader,
            $fixturesLoader,
            $fixturesFinder,
            $bundlesResolver
        );
    }
}
