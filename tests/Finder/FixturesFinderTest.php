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

use Hautelook\AliceBundle\Finder\FixturesFinder;
use Hautelook\AliceBundle\Tests\KernelTestCase;
use Hautelook\AliceBundle\Tests\Functional\TestBundle\Bundle\ABundle\TestABundle;
use Hautelook\AliceBundle\Tests\Functional\TestBundle\Bundle\BBundle\TestBBundle;
use Hautelook\AliceBundle\Tests\Functional\TestBundle\Bundle\CBundle\TestCBundle;
use Hautelook\AliceBundle\Tests\Functional\TestBundle\Bundle\EmptyBundle\TestEmptyBundle;
use Hautelook\AliceBundle\Tests\Functional\TestBundle\TestBundle;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @coversDefaultClass Hautelook\AliceBundle\Finder\FixturesFinder
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class FixturesFinderTest extends KernelTestCase
{
    protected function setUp()
    {
        self::bootKernel();
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
        $finder = new FixturesFinder('DataFixtures/ORM');
        $kernelProphecy = $this->prophesize('Symfony\Component\HttpKernel\KernelInterface');

        try {
            $fixtures = $finder->getFixtures($kernelProphecy->reveal(), $bundles, $environment);

            $this->assertCount(0, array_diff($expected, $fixtures));
        } catch (\InvalidArgumentException $exception) {
            if (0 !== count($expected)) {
                throw $exception;
            }
        }
    }

    /**
     * @cover ::resolveFixtures
     * @dataProvider unresolvedFixturesProvider
     *
     * @param array $fixtures
     * @param array $expected
     */
    public function testResolveFixtures(array $fixtures, array $expected)
    {
        $finder = new FixturesFinder('DataFixtures/ORM');

        try {
            $actual = $finder->resolveFixtures(self::$kernel, $fixtures);

            if (0 === count($expected)) {
                $this->fail('Expected an exception to be thrown.');
            }

            $this->assertSame($expected, $actual);
        } catch (\InvalidArgumentException $exception) {
            if (0 !== count($expected)) {
                $this->fail($exception->getMessage());
            }
        } catch (\RuntimeException $exception) {
            if (0 !== count($expected)) {
                $this->fail($exception->getMessage());
            }
            // Otherwise is the expected result
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
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/dummy.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/product.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/Dev/dev.yml',
            ],
        ];

        $data[] = [
            [
                new TestBundle(),
            ],
            'Dev',
            [
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/brand.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/dummy.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/product.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/Dev/dev.yml',
            ],
        ];

        $data[] = [
            [
                new TestBundle(),
            ],
            'inte',
            [
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/brand.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/dummy.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/product.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/Inte/inte.yml',
            ],
        ];

        $data[] = [
            [
                new TestBundle(),
            ],
            'prod',
            [
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/brand.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/dummy.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/product.yml',
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/Prod/prod.yml',
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
            [],
        ];

        $data[] = [
            [
                new TestABundle(),
                new TestCBundle(),
            ],
            'dev',
            [
                '/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.php',
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

    public function unresolvedFixturesProvider()
    {
        $data = [];

        // Valid file
        $data[] = [
            [getcwd().'/tests/Functional/TestBundle/DataFixtures/ORM/brand.yml'],
            ['/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/brand.yml'],
        ];
        // Valid file with unresolved path
        $data[] = [
            [getcwd().'/tests/Functional/TestBundle/Entity/../DataFixtures/ORM/brand.yml'],
            ['/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/brand.yml'],
        ];
        // Unknown file
        $data[] = [
            [getcwd().'/tests/Functional/TestBundle/DataFixtures/ORM/unknown.yml'],
            [],
        ];
        // directory
        $data[] = [
            [getcwd().'/tests/Functional/TestBundle/DataFixtures/ORM'],
            [],
        ];

        //
        // '@' annotation
        //
        // Valid file
        $data[] = [
            ['@TestBundle/DataFixtures/ORM/brand.yml'],
            ['/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/brand.yml'],
        ];
        // Valid file with unresolved path
        $data[] = [
            ['@TestBundle/Entity/../DataFixtures/ORM/brand.yml'],
            [],
        ];
        // Unknown file
        $data[] = [
            ['@TestBundle/DataFixtures/ORM/unknown.yml'],
            [],
        ];
        // directory
        $data[] = [
            ['@TestBundle/DataFixtures/ORM'],
            [],
        ];

        //
        // SplInfo support
        //
        // Valid file
        $data[] = [
            [new \SplFileInfo(getcwd().'/tests/Functional/TestBundle/DataFixtures/ORM/brand.yml')],
            ['/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/brand.yml'],
        ];
        // Valid file with unresolved path
        $data[] = [
            [new \SplFileInfo(getcwd().'/tests/Functional/TestBundle/Entity/../DataFixtures/ORM/brand.yml')],
            ['/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/brand.yml'],
        ];
        // Unknown file
        $data[] = [
            [new \SplFileInfo(getcwd().'/tests/Functional/TestBundle/DataFixtures/ORM/unknown.yml')],
            [],
        ];
        // directory
        $data[] = [
            [new \SplFileInfo(getcwd().'/tests/Functional/TestBundle/DataFixtures/ORM')],
            [],
        ];
        // With the @ annotation
        $data[] = [
            [new \SplFileInfo('@TestBundle/DataFixtures/ORM/brand.yml')],
            [],
        ];

        //
        // Symfony SplInfo support
        //
        // Valid file
        $data[] = [
            [
                new SymfonySplFileInfo(
                    getcwd().'/tests/Functional/TestBundle/DataFixtures/ORM/brand.yml',
                    getcwd().'/tests/Functional/TestBundle/DataFixtures/ORM/brand.yml',
                    'brand.yml'
                ),
            ],
            ['/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/brand.yml'],
        ];
        // Valid file with unresolved path
        $data[] = [
            [
                new SymfonySplFileInfo(
                    getcwd().'/tests/Functional/TestBundle/Entity/../DataFixtures/ORM/brand.yml',
                    getcwd().'/tests/Functional/TestBundle/Entity/../DataFixtures/ORM/brand.yml',
                    'brand.yml'
                ),
            ],
            ['/home/travis/build/theofidry/AliceBundle/tests/Functional/TestBundle/DataFixtures/ORM/brand.yml'],
        ];
        // Unknown file
        $data[] = [
            [
                new SymfonySplFileInfo(
                    getcwd().'/tests/Functional/TestBundle/DataFixtures/ORM/unknown.yml',
                    getcwd().'/tests/Functional/TestBundle/DataFixtures/ORM/unknown.yml',
                    'unknown.yml'
                ),
            ],
            [],
        ];
        // directory
        $data[] = [
            [
                new SymfonySplFileInfo(
                    getcwd().'/tests/Functional/TestBundle/DataFixtures/ORM',
                    getcwd().'/tests/Functional/TestBundle/DataFixtures/ORM',
                    'ORM'
                ),
            ],
            [],
        ];
        // With the @ annotation
        $data[] = [
            [
                new SymfonySplFileInfo(
                    '@TestBundle/DataFixtures/ORM/brand.yml',
                    '@TestBundle/DataFixtures/ORM/brand.yml',
                    'brand.yml'
                ),
            ],
            [],
        ];

        // Non string or SqlInfo instance
        $data[] = [
            [
                new \stdClass(),
            ],
            [],
        ];

        // Fix paths
        foreach ($data as $index => $dataSet) {
            $data[$index][1] = str_replace('/home/travis/build/theofidry/AliceBundle', getcwd(), $dataSet[1]);
        }

        return $data;
    }
}
