<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Tests\Alice\DataFixtures;

use Hautelook\AliceBundle\Alice\DataFixtures\Loader;
use Hautelook\AliceBundle\Alice\DataFixtures\LoadingLimitException;

/**
 * @coversDefaultClass Hautelook\AliceBundle\Alice\DataFixtures\Loader
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class LoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @cover ::__construct
     */
    public function testConstruct()
    {
        $aliceLoaderProphecy = $this->prophesize('Hautelook\AliceBundle\Alice\DataFixtures\Fixtures\Loader');

        $loader = new Loader($aliceLoaderProphecy->reveal(), ['dummyProcessor'], false);

        $this->assertEquals(['dummyProcessor'], $loader->getProcessors());
        $this->assertFalse($loader->getPersistOnce());


        $aliceLoaderProphecy = $this->prophesize('Hautelook\AliceBundle\Alice\DataFixtures\Fixtures\Loader');

        $loader = new Loader($aliceLoaderProphecy->reveal(), [], true);

        $this->assertEquals([], $loader->getProcessors());
        $this->assertTrue($loader->getPersistOnce());
    }

    /**
     * @cover ::load
     * @cover ::persist
     */
    public function testLoadEmptyFixturesSet()
    {
        $aliceLoaderProphecy = $this->prophesize('Hautelook\AliceBundle\Alice\DataFixtures\Fixtures\Loader');

        $persisterProphecy = $this->prophesize('Nelmio\Alice\PersisterInterface');
        
        $loader = new Loader($aliceLoaderProphecy->reveal(), ['dummyProcessor'], false);
        $objects = $loader->load($persisterProphecy->reveal(), []);

        $this->assertEquals([], $objects);
    }

    /**
     * @cover ::load
     * @cover ::persist
     */
    public function testLoadWithFixtures()
    {
        $object = new \stdClass();

        $oldPersister = $this->prophesize('Nelmio\Alice\PersisterInterface')->reveal();

        $persisterProphecy = $this->prophesize('Nelmio\Alice\PersisterInterface');
        $persisterProphecy->persist([$object])->shouldBeCalled();

        $fixturesLoaderProphecy = $this->prophesize('Hautelook\AliceBundle\Alice\DataFixtures\Fixtures\Loader');
        $fixturesLoaderProphecy->getPersister()->willReturn($oldPersister);
        $fixturesLoaderProphecy->load('random/file', [])->willReturn([$object]);
        $fixturesLoaderProphecy->setPersister($persisterProphecy->reveal())->shouldBeCalled();
        $fixturesLoaderProphecy->setPersister($oldPersister)->shouldBeCalled();

        $loader = new Loader($fixturesLoaderProphecy->reveal(), [], false);
        $objects = $loader->load($persisterProphecy->reveal(), ['random/file']);

        $this->assertEquals([$object], $objects);
    }

    /**
     * @cover ::load
     * @cover ::persist
     */
    public function testLoadWithPersistOnceAtFalse()
    {
        $objects = [
            new \stdClass(),
            new \stdClass(),
        ];

        $oldPersister = $this->prophesize('Nelmio\Alice\PersisterInterface')->reveal();

        $persisterProphecy = $this->prophesize('Nelmio\Alice\PersisterInterface');
        $persisterProphecy->persist([$objects[0]])->shouldBeCalled();
        $persisterProphecy->persist([$objects[1]])->shouldBeCalled();

        $fixturesLoaderProphecy = $this->prophesize('Hautelook\AliceBundle\Alice\DataFixtures\Fixtures\Loader');
        $fixturesLoaderProphecy->getPersister()->willReturn($oldPersister);
        $fixturesLoaderProphecy->load('random/file1', [])->willReturn([$objects[0]]);
        $fixturesLoaderProphecy->load('random/file2', [])->willReturn([$objects[0]]);
        $fixturesLoaderProphecy->setPersister($persisterProphecy->reveal())->shouldBeCalled();
        $fixturesLoaderProphecy->setPersister($oldPersister)->shouldBeCalled();

        $loader = new Loader($fixturesLoaderProphecy->reveal(), [], false);
        $objects = $loader->load(
            $persisterProphecy->reveal(),
            [
                'random/file1',
                'random/file2',
            ]
        );

        $this->assertEquals($objects, $objects);
    }

    /**
     * @cover ::load
     * @cover ::persist
     */
    public function testLoadWithPersistOnceAtTrue()
    {
        $objects = [
            new \stdClass(),
            new \stdClass(),
        ];

        $oldPersister = $this->prophesize('Nelmio\Alice\PersisterInterface')->reveal();

        $persisterProphecy = $this->prophesize('Nelmio\Alice\PersisterInterface');
        $persisterProphecy->persist($objects)->shouldBeCalled();

        $fixturesLoaderProphecy = $this->prophesize('Hautelook\AliceBundle\Alice\DataFixtures\Fixtures\Loader');
        $fixturesLoaderProphecy->getPersister()->willReturn($oldPersister);
        $fixturesLoaderProphecy->load('random/file1', [])->willReturn([$objects[0]]);
        $fixturesLoaderProphecy->load('random/file2', [])->willReturn([$objects[0]]);
        $fixturesLoaderProphecy->setPersister($persisterProphecy->reveal())->shouldBeCalled();
        $fixturesLoaderProphecy->setPersister($oldPersister)->shouldBeCalled();

        $loader = new Loader($fixturesLoaderProphecy->reveal(), [], true);
        $objects = $loader->load(
            $persisterProphecy->reveal(),
            [
                'random/file1',
                'random/file2',
            ]
        );

        $this->assertEquals($objects, $objects);
    }

    /**
     * @cover ::load
     * @cover ::persist
     */
    public function testLoadWithFixturesAndProcessors()
    {
        $object = new \stdClass();

        $oldPersister = $this->prophesize('Nelmio\Alice\PersisterInterface')->reveal();

        $persisterProphecy = $this->prophesize('Nelmio\Alice\PersisterInterface');
        $persisterProphecy->persist([$object])->shouldBeCalled();

        $fixturesLoaderProphecy = $this->prophesize('Hautelook\AliceBundle\Alice\DataFixtures\Fixtures\Loader');
        $fixturesLoaderProphecy->getPersister()->willReturn($oldPersister);
        $fixturesLoaderProphecy->load('random/file', [])->willReturn([$object]);
        $fixturesLoaderProphecy->setPersister($persisterProphecy->reveal())->shouldBeCalled();
        $fixturesLoaderProphecy->setPersister($oldPersister)->shouldBeCalled();

        $processorProphecy = $this->prophesize('Nelmio\Alice\ProcessorInterface');
        $processorProphecy->preProcess($object)->shouldBeCalled();
        $processorProphecy->postProcess($object)->shouldBeCalled();

        $loader = new Loader($fixturesLoaderProphecy->reveal(), [$processorProphecy->reveal()], false);
        $objects = $loader->load($persisterProphecy->reveal(), ['random/file']);

        $this->assertEquals([$object], $objects);
    }

    public function testLoaderInterface()
    {
        $object = new \stdClass();

        $persisterProphecy = $this->prophesize('Nelmio\Alice\PersisterInterface');
        $persisterProphecy->persist([$object])->shouldBeCalled();

        $fixturesLoaderProphecy = $this->prophesize('Hautelook\AliceBundle\Alice\DataFixtures\Fixtures\LoaderInterface');
        $fixturesLoaderProphecy->load('random/file', [])->willReturn([$object]);

        $loader = new Loader($fixturesLoaderProphecy->reveal(), [], false);
        $objects = $loader->load($persisterProphecy->reveal(), ['random/file']);

        $this->assertEquals([$object], $objects);
    }

    /**
     * @expectedException \Hautelook\AliceBundle\Alice\DataFixtures\LoadingLimitException
     */
    public function testLoaderLimit()
    {
        $persisterProphecy = $this->prophesize('Nelmio\Alice\PersisterInterface');
        $persisterProphecy->persist()->shouldNotBeCalled();

        $fixturesLoaderProphecy = $this->prophesize('Hautelook\AliceBundle\Alice\DataFixtures\Fixtures\LoaderInterface');
        $fixturesLoaderProphecy->load('random/file', [])->willThrow(new \UnexpectedValueException());
        $fixturesLoaderProphecy->load('random/file', [])->shouldBeCalledTimes(6);

        $loader = new Loader($fixturesLoaderProphecy->reveal(), [], false);
        $loader->load($persisterProphecy->reveal(), ['random/file']);
    }

    /**
     * @expectedException \Hautelook\AliceBundle\Alice\DataFixtures\LoadingLimitException
     */
    public function testLoaderWithCustomLimit()
    {
        $persisterProphecy = $this->prophesize('Nelmio\Alice\PersisterInterface');
        $persisterProphecy->persist()->shouldNotBeCalled();

        $fixturesLoaderProphecy = $this->prophesize('Hautelook\AliceBundle\Alice\DataFixtures\Fixtures\LoaderInterface');
        $fixturesLoaderProphecy->load('random/file', [])->willThrow(new \UnexpectedValueException());
        $fixturesLoaderProphecy->load('random/file', [])->shouldBeCalledTimes(11);

        $loader = new Loader($fixturesLoaderProphecy->reveal(), [], false);
        $loader->setLoadingLimit(10);
        $loader->load($persisterProphecy->reveal(), ['random/file']);
    }
}
