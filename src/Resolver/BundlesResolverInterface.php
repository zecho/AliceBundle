<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Resolver;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * The BundlesResolver is the class responsible for retrieving the bundles registered to an application from their names.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface BundlesResolverInterface
{
    /**
     * Looks at all the bundles registered in the application to return the bundles requested. An exception is thrown
     * if a bundle has not been found.
     *
     * @param Application $application Application in which bundles will be looked in.
     * @param string[]    $names       Bundle names.
     *
     * @return BundleInterface[] Bundles requested.
     *
     * @throws \RuntimeException A bundle could not be resolved.
     */
    public function resolveBundles(Application $application, array $names);
}
