<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Tests\Finder;

use Hautelook\AliceBundle\Finder\Finder;

/**
 * @coversDefaultClass Hautelook\AliceBundle\Finder\Finder
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class FinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @cover ::resolveBundles
     */
    public function testResolveBundles()
    {
        $finder = new Finder();

        $kernel = $this->prophesize('Symfony\Component\HttpKernel\KernelInterface');
        $kernel->getBundles()->willReturn(
            [
                'ABundle' => 'ABundleInstance',
                'BBundle' => 'BBundleInstance',
                'CBundle' => 'CBundleInstance',
            ]
        );
        $application = $this->prophesize('Symfony\Bundle\FrameworkBundle\Console\Application');
        $application->getKernel()->willReturn($kernel->reveal());

        $bundles = $finder->resolveBundles($application->reveal(), ['ABundle']);
        $this->assertEquals(['ABundle' => 'ABundleInstance'], $bundles);

        $bundles = $finder->resolveBundles($application->reveal(), ['ABundle', 'BBundle']);
        $this->assertEquals(['ABundle' => 'ABundleInstance', 'BBundle' => 'BBundleInstance'], $bundles);

        try {
            $bundles = $finder->resolveBundles($application->reveal(), ['UnknownBundle']);
            $this->fail('Expected exception to be thrown');
        } catch (\RuntimeException $exception) {
            // Expected result
        }

        try {
            $bundles = $finder->resolveBundles($application->reveal(), ['ABundle', 'UnknownBundle']);
            $this->fail('Expected exception to be thrown');
        } catch (\RuntimeException $exception) {
            // Expected result
        }
    }
}
