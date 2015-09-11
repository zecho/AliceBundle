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

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class BundlesResolver implements BundlesResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolveBundles(Application $application, array $names)
    {
        $bundles = $application->getKernel()->getBundles();

        $result = [];
        foreach ($names as $name) {
            if (false === isset($bundles[$name])) {
                throw new \RuntimeException(sprintf(
                    'The bundle "%s" was not found. Bundles available are: %s.',
                    $name,
                    implode('", "', array_keys($bundles))
                ));
            }

            $result[$name] = $bundles[$name];
        }

        return $result;
    }
}
