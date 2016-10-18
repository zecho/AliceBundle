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
use Hautelook\AliceBundle\Locator\EnvFilesLocator;
use Hautelook\AliceBundle\Locator\EnvFilesLocator\DummyBundle;
use Hautelook\AliceBundle\Locator\LocatorRegistry;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \Hautelook\AliceBundle\Locator\LocatorRegistry
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class LocatorRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function testIsAFixtureLocator()
    {
        $this->assertTrue(is_a(LocatorRegistry::class, FixtureLocatorInterface::class, true));
    }

    /**
     * @expectedException \TypeError
     */
    public function testThrowsAnExceptionIfArgumentIsNotALocator()
    {
        new LocatorRegistry([new \stdClass()]);
    }

    /**
     * @expectedException \DomainException
     */
    public function testIsNotClonable()
    {
        clone new LocatorRegistry([]);
    }

    public function testConcateneAllLocatorFiles()
    {
        $bundles = ['ABdundle', 'BBundle'];
        $env = 'dev';

        /** @var FixtureLocatorInterface|ObjectProphecy $firstLocatorProphecy */
        $firstLocatorProphecy = $this->prophesize(FixtureLocatorInterface::class);
        $firstLocatorProphecy->locateFiles($bundles, $env)->willReturn(['/path/to/file1.yml', '/path/to/file2.yml']);
        /** @var FixtureLocatorInterface $firstLocator */
        $firstLocator = $firstLocatorProphecy->reveal();

        /** @var FixtureLocatorInterface|ObjectProphecy $secondLocatorProphecy */
        $secondLocatorProphecy = $this->prophesize(FixtureLocatorInterface::class);
        $secondLocatorProphecy->locateFiles($bundles, $env)->willReturn(['/path/to/file2.yml', '/path/to/file3.yml']);
        /** @var FixtureLocatorInterface $secondLocator */
        $secondLocator = $secondLocatorProphecy->reveal();

        $expected = [
            '/path/to/file1.yml',
            '/path/to/file2.yml',
            '/path/to/file3.yml',
        ];

        $locator = new LocatorRegistry([$firstLocator, $secondLocator]);
        $actual = $locator->locateFiles($bundles, $env);

        $this->assertEquals($expected, $actual);

        $firstLocatorProphecy->locateFiles(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $secondLocatorProphecy->locateFiles(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }
}
