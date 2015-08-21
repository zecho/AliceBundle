<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Tests\Doctrine\Finder;

use Hautelook\AliceBundle\Doctrine\Finder\Finder;
use Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Bundle\ABundle\TestABundle;
use Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Bundle\BBundle\TestBBundle;
use Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Bundle\CBundle\TestCBundle;
use Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Bundle\EmptyBundle\TestEmptyBundle;
use Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\TestBundle;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @coversDefaultClass Hautelook\AliceBundle\Doctrine\Finder\Finder
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
            $finder->resolveBundles($application->reveal(), ['UnknownBundle']);
            $this->fail('Expected exception to be thrown');
        } catch (\RuntimeException $exception) {
            // Expected result
        }

        try {
            $finder->resolveBundles($application->reveal(), ['ABundle', 'UnknownBundle']);
            $this->fail('Expected exception to be thrown');
        } catch (\RuntimeException $exception) {
            // Expected result
        }
    }

    /**
     * @cover ::getFixtures
     * @cover ::getLoadersPaths
     * @cover ::getFixturesFromDirectory
     * @cover ::resolveFixtures
     * @dataProvider fixturesProvider
     *
     * @param BundleInterface[] $bundles
     * @param string            $environment
     * @param string[]          $expected
     */
    public function testGetFixtures(array $bundles, $environment, array $expected)
    {
        $finder = new Finder();
        $kernel = $this->prophesize('Symfony\Component\HttpKernel\KernelInterface');
        $kernel->locateResource('@TestABundle/DataFixtures/ORM/aentity.yml', null, true)->willReturn(
            str_replace(
                '/home/travis/build/theofidry/AliceBundle',
                getcwd(),
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.yml'
            )
        );
        $kernel->locateResource('@TestBBundle/DataFixtures/ORM/bentity.yml', null, true)->willReturn(
            str_replace(
                '/home/travis/build/theofidry/AliceBundle',
                getcwd(),
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml'
            )
        );

        try {
            $fixtures = $finder->getFixtures($kernel->reveal(), $bundles, $environment);

            sort($expected);
            sort($fixtures);

            $this->assertEquals($expected, $fixtures);
        } catch (\InvalidArgumentException $exception) {
            if (0 !== count($expected)) {
                throw $exception;
            }
        }
    }

    /**
     * @cover ::getFixtures
     */
    public function testGetFixturesWithInvalidPath()
    {
        $finder = new Finder();
        $kernel = $this->prophesize('Symfony\Component\HttpKernel\KernelInterface');

        try {
            $finder->getFixtures($kernel->reveal(), [new TestEmptyBundle()], 'dev');
            $this->fail('Expected \InvalidArgumentException to be thrown.');
        } catch (\InvalidArgumentException $exception) {
            // Expected result
        }
    }

    public function fixturesProvider()
    {
        $return = [];

        $return[] = [
            [
                new TestBundle(),
            ],
            'dev',
            [
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/brand.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/Dev/dev.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/product.yml',
            ]
        ];

        $return[] = [
            [
                new TestBundle(),
            ],
            'Dev',
            [
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/brand.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/Dev/dev.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/product.yml',
            ]
        ];

        $return[] = [
            [
                new TestBundle(),
            ],
            'inte',
            [
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/brand.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/Inte/inte.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/product.yml',
            ]
        ];

        $return[] = [
            [
                new TestBundle(),
            ],
            'prod',
            [
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/brand.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/Prod/prod.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/product.yml',
            ]
        ];

        $return[] = [
            [
                new TestABundle(),
            ],
            'dev',
            [
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.yml',
            ]
        ];

        $return[] = [
            [
                new TestBBundle(),
            ],
            'dev',
            [
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml',
            ]
        ];

        $return[] = [
            [
                new TestCBundle(),
            ],
            'dev',
            [
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml',
            ]
        ];

        $return[] = [
            [
                new TestCBundle(),
            ],
            'CEnv',
            [
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/CBundle/DataFixtures/ORM/CEnv/empty.yml',
            ]
        ];

        $return[] = [
            [
                new TestCBundle(),
            ],
            'DEnv',
            [
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/CBundle/DataFixtures/ORM/DEnv/products/product1.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/CBundle/DataFixtures/ORM/DEnv/products/product2.yml',
            ]
        ];

        $return[] = [
            [
                new TestCBundle(),
            ],
            'CEnv',
            [
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/CBundle/DataFixtures/ORM/CEnv/empty.yml',
            ]
        ];

        $return[] = [
            [
                new TestBundle(),
                new TestCBundle(),
            ],
            'CEnv',
            [
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/brand.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/product.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/CBundle/DataFixtures/ORM/CEnv/empty.yml',
            ]
        ];

        $return[] = [
            [
                new TestCBundle(),
            ],
            'EEnv',
            [
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml',
                '/home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/CBundle/DataFixtures/ORM/EEnv/empty.yml',
            ]
        ];

        // Fix paths
        foreach ($return as $index => $dataSet) {
            foreach ($dataSet[2] as $dataSetIndex => $filePath) {
                $return[$index][2][$dataSetIndex] = str_replace(
                    '/home/travis/build/theofidry/AliceBundle',
                    getcwd(),
                    $dataSet[2][$dataSetIndex]
                );
            }
        }

        return $return;
    }
}
