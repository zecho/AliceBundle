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

use Hautelook\AliceBundle\Doctrine\Finder\FixturesFinder;
use Hautelook\AliceBundle\Tests\Functional\TestBundle\Bundle\ABundle\TestABundle;
use Hautelook\AliceBundle\Tests\Functional\TestBundle\Bundle\BBundle\TestBBundle;
use Hautelook\AliceBundle\Tests\Functional\TestBundle\Bundle\CBundle\TestCBundle;
use Hautelook\AliceBundle\Tests\Functional\TestBundle\Bundle\EmptyBundle\TestEmptyBundle;
use Hautelook\AliceBundle\Tests\Functional\TestBundle\TestBundle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @coversDefaultClass Hautelook\AliceBundle\Doctrine\Finder\FixturesFinder
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class FixturesFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FixturesFinder
     */
    private $finder;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->finder = new FixturesFinder('DataFixtures/ORM');

        $containerProphecy = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');
        $containerProphecy->get()->shouldNotBeCalled();

        /* @var $container ContainerInterface */
        $container = $containerProphecy->reveal();

        $this->finder->setContainer($container);
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
        $kernel = $this->prophesize('Symfony\Component\HttpKernel\KernelInterface');
        $kernel->locateResource('@TestABundle/DataFixtures/ORM/aentity.php', null, true)->willReturn(
            getcwd().'/tests/Functional/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.php'
        );
        $kernel->locateResource('@TestBBundle/DataFixtures/ORM/bentity.yml', null, true)->willReturn(
            getcwd().'/tests/Functional/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml'
        );

        try {
            $fixtures = $this->finder->getFixtures($kernel->reveal(), $bundles, $environment);

            $this->assertCount(0, array_diff($expected, $fixtures));
        } catch (\InvalidArgumentException $exception) {
            if (0 !== count($expected)) {
                throw $exception;
            }
        }
    }

    /**
     * @cover ::getDataLoaders
     * @dataProvider dataLoadersProvider
     */
    public function testGetDataLoaders($bundles, $environment, $expected)
    {
        $loaders = $this->finder->getDataLoaders($bundles, $environment);

        try {
            foreach ($loaders as $index => $loader) {
                $loaders[$index] = get_class($loader);
            }

            $this->assertCount(0, array_diff($expected, $loaders));
        } catch (\InvalidArgumentException $exception) {
            if (0 !== count($expected)) {
                throw $exception;
            }
        }
    }

    public function fixturesProvider()
    {
        $data = [];

        $data[] = [
            [
                new TestBundle(),
            ],
            'dev',
            [
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/brand.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/Dev/dev.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/product.yml',
            ],
        ];

        $data[] = [
            [
                new TestBundle(),
            ],
            'Dev',
            [
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/brand.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/Dev/dev.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/product.yml',
            ],
        ];

        $data[] = [
            [
                new TestBundle(),
            ],
            'inte',
            [
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/brand.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/Inte/inte.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/product.yml',
            ],
        ];

        $data[] = [
            [
                new TestBundle(),
            ],
            'prod',
            [
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/brand.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/Prod/prod.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/product.yml',
            ],
        ];

        $data[] = [
            [
                new TestABundle(),
            ],
            'dev',
            [
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.php',
            ],
        ];

        $data[] = [
            [
                new TestBBundle(),
            ],
            'dev',
            [
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml',
            ],
        ];

        $data[] = [
            [
                new TestCBundle(),
            ],
            'dev',
            [
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.php',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml',
            ],
        ];

        $data[] = [
            [
                new TestCBundle(),
            ],
            'CEnv',
            [
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.php',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/CBundle/DataFixtures/ORM/CEnv/empty.yml',
            ],
        ];

        $data[] = [
            [
                new TestCBundle(),
            ],
            'DEnv',
            [
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.php',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/CBundle/DataFixtures/ORM/DEnv/products/product1.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/CBundle/DataFixtures/ORM/DEnv/products/product2.yml',
            ],
        ];

        $data[] = [
            [
                new TestCBundle(),
            ],
            'CEnv',
            [
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.php',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/CBundle/DataFixtures/ORM/CEnv/empty.yml',
            ],
        ];

        $data[] = [
            [
                new TestBundle(),
                new TestCBundle(),
            ],
            'CEnv',
            [
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/brand.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/product.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.php',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/CBundle/DataFixtures/ORM/CEnv/empty.yml',
            ],
        ];

        $data[] = [
            [
                new TestCBundle(),
            ],
            'EEnv',
            [
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.php',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/CBundle/DataFixtures/ORM/EEnv/empty.yml',
            ],
        ];

        // Fix paths
        foreach ($data as $index => $dataSet) {
            foreach ($dataSet[2] as $dataSetIndex => $filePath) {
                $data[$index][2][$dataSetIndex] = str_replace(
                    '/home/travis/build/theofidry/AliceBundle',
                    getcwd(),
                    $dataSet[2][$dataSetIndex]
                );
            }
        }

        return $data;
    }

    public function dataLoadersProvider()
    {
        $return = [];

        $return[] = [
            [
                new TestBundle(),
            ],
            'dev',
            [
                'Hautelook\AliceBundle\Tests\Functional\TestBundle\DataFixtures\ORM\DataLoader',
            ],
        ];

        $return[] = [
            [
                new TestBundle(),
            ],
            'ignored',
            [
                'Hautelook\AliceBundle\Tests\Functional\TestBundle\DataFixtures\ORM\DataLoader',
                'Hautelook\AliceBundle\Tests\Functional\TestBundle\DataFixtures\ORM\Ignored\DataLoader',
            ],
        ];

        $return[] = [
            [
                new TestABundle(),
            ],
            'dev',
            [],
        ];

        return $return;
    }
}
