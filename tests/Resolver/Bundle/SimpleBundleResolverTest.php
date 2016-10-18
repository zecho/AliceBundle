<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Resolver\Bundle;

use Hautelook\AliceBundle\BundleResolverInterface;
use Hautelook\AliceBundle\Resolver\ResolverKernel;
use Hautelook\AliceBundle\Resolver\Bundle\SimpleBundleResolver;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * @covers \Hautelook\AliceBundle\Resolver\SimpleBundleResolver
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class SimpleBundleResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testIsABundleResolver()
    {
        $this->assertTrue(is_a(SimpleBundleResolver::class, BundleResolverInterface::class, true));
    }

    /**
     * @expectedException \DomainException
     */
    public function testIsNotClonable()
    {
        clone new SimpleBundleResolver();
    }

    public function testCanResolveBundles()
    {
        $kernel = new ResolverKernel(__FUNCTION__, true);
        $kernel->boot();
        $application = new Application($kernel);

        $resolver = new SimpleBundleResolver();
        $result = $resolver->resolveBundles($application, ['ABundle']);
        $this->assertSame(
            [
                $kernel->getBundle('ABundle'),
            ],
            $result
        );

        $kernel->shutdown();
    }

    /**
     * @expectedException \Hautelook\AliceBundle\Exception\Resolver\BundleNotFoundException
     * @expectedExceptionMessage The bundle "ABundle" was not found. Bundles available are: [].
     */
    public function testThrowsAnExceptionWhenBundleCoudlNotBeFound()
    {
        $kernel = new ResolverKernel(__FUNCTION__, true);
        $application = new Application($kernel);
        $resolver = new SimpleBundleResolver();
        $resolver->resolveBundles($application, ['ABundle']);   // Will not be found as the kernel is not booted
    }
}
