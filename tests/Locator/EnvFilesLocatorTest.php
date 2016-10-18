<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Locator;

use Hautelook\AliceBundle\FixtureLocatorInterface;
use Hautelook\AliceBundle\Locator\EnvFilesLocator\DummyBundle;

/**
 * @covers \Hautelook\AliceBundle\Locator\EnvFilesLocator
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class EnvFilesLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsAFixtureLocator()
    {
        $this->assertTrue(is_a(EnvFilesLocator::class, FixtureLocatorInterface::class, true));
    }

    /**
     * @expectedException \DomainException
     */
    public function testIsNotClonable()
    {
        clone new EnvFilesLocator('');
    }

    /**
     * @dataProvider provideSets
     */
    public function testGetFilesFromABundle(array $bundles, string $environment, string $path, array $expected)
    {
        $locator = new EnvFilesLocator($path);
        $actual = $locator->locateFiles($bundles, $environment);

        $this->assertEquals($expected, $actual, '', 0.0, 10, true);
    }

    public function provideSets()
    {
        $prefix = realpath(__DIR__.'/../../fixtures/Locator/EnvFilesLocator');

        yield 'dev environment' => [
            [new DummyBundle()],
            'dev',
            '../',
            [
                $prefix.'/f.dev.file4.yml',
                $prefix.'/file2.dev.yml',
                $prefix.'/file3.dev.test.yml',
                $prefix.'/file3.test.dev.yml',
            ]
        ];

        yield 'test environment' => [
            [new DummyBundle()],
            'test',
            '../',
            [
                $prefix.'/file3.dev.test.yml',
                $prefix.'/file3.test.dev.yml',
            ]
        ];

        yield 'test environment' => [
            [new DummyBundle()],
            'unknown',
            '../',
            []
        ];
    }
}
