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
use Hautelook\AliceBundle\Alice\ProcessorChain;

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

        $processors = [$this->getMock('Nelmio\Alice\ProcessorInterface')];
        $loader = new Loader($aliceLoaderProphecy->reveal(), new ProcessorChain($processors), false, 5);

        $this->assertSame($processors, $loader->getProcessors());
        $this->assertFalse($loader->getPersistOnce());

        $aliceLoaderProphecy = $this->prophesize('Hautelook\AliceBundle\Alice\DataFixtures\Fixtures\Loader');

        $loader = new Loader($aliceLoaderProphecy->reveal(), new ProcessorChain([]), true, 5);

        $this->assertSame([], $loader->getProcessors());
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

        $processors = [$this->getMock('Nelmio\Alice\ProcessorInterface')];
        $loader = new Loader($aliceLoaderProphecy->reveal(), new ProcessorChain($processors), false, 5);
        $objects = $loader->load($persisterProphecy->reveal(), []);

        $this->assertSame([], $objects);
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

        $loader = new Loader($fixturesLoaderProphecy->reveal(), new ProcessorChain([]), false, 5);
        $objects = $loader->load($persisterProphecy->reveal(), ['random/file']);

        $this->assertSame([$object], $objects);
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

        $loader = new Loader($fixturesLoaderProphecy->reveal(), new ProcessorChain([]), false, 5);
        $objects = $loader->load(
            $persisterProphecy->reveal(),
            [
                'random/file1',
                'random/file2',
            ]
        );

        $this->assertSame($objects, $objects);
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

        $loader = new Loader($fixturesLoaderProphecy->reveal(), new ProcessorChain([]), true, 5);
        $objects = $loader->load(
            $persisterProphecy->reveal(),
            [
                'random/file1',
                'random/file2',
            ]
        );

        $this->assertSame($objects, $objects);
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

        $loader = new Loader($fixturesLoaderProphecy->reveal(), new ProcessorChain([$processorProphecy->reveal()]), false, 5);
        $objects = $loader->load($persisterProphecy->reveal(), ['random/file']);

        $this->assertSame([$object], $objects);
    }

    public function testLoaderInterface()
    {
        $object = new \stdClass();

        $persisterProphecy = $this->prophesize('Nelmio\Alice\PersisterInterface');
        $persisterProphecy->persist([$object])->shouldBeCalled();

        $fixturesLoaderProphecy = $this->prophesize('Hautelook\AliceBundle\Alice\DataFixtures\Fixtures\LoaderInterface');
        $fixturesLoaderProphecy->load('random/file', [])->willReturn([$object]);

        $loader = new Loader($fixturesLoaderProphecy->reveal(), new ProcessorChain([]), false, 5);
        $objects = $loader->load($persisterProphecy->reveal(), ['random/file']);

        $this->assertSame([$object], $objects);
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

        $loader = new Loader($fixturesLoaderProphecy->reveal(), new ProcessorChain([]), false, 5);
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

        $loader = new Loader($fixturesLoaderProphecy->reveal(), new ProcessorChain([]), false, 10);
        $loader->load($persisterProphecy->reveal(), ['random/file']);
    }

    /**
     * @covers ::load()
     * @covers ::registerErrorMessage()
     */
    public function testLoaderLimitWithMessages()
    {
        $this->setExpectedException(
            '\Hautelook\AliceBundle\Alice\DataFixtures\LoadingLimitException',
            'Loading files limit of 3 reached. Could not load the following files:'.PHP_EOL
            .'another/file:'.PHP_EOL
            .' - That is a failed'.PHP_EOL
            .'empty/message'.PHP_EOL
            .'random/file:'.PHP_EOL
            .' - Some dummy message'
        );

        $persisterProphecy = $this->prophesize('Nelmio\Alice\PersisterInterface');
        $persisterProphecy->persist()->shouldNotBeCalled();

        $fixturesLoaderProphecy = $this->prophesize('Hautelook\AliceBundle\Alice\DataFixtures\Fixtures\LoaderInterface');
        $fixturesLoaderProphecy->load('random/file', [])->willThrow(new \UnexpectedValueException('Some dummy message'));
        $fixturesLoaderProphecy->load('another/file', [])->willThrow(new \UnexpectedValueException('That is a failed'));
        $fixturesLoaderProphecy->load('empty/message', [])->willThrow(new \UnexpectedValueException());

        $loader = new Loader($fixturesLoaderProphecy->reveal(), new ProcessorChain([]), false, 3);
        $loader->load($persisterProphecy->reveal(), ['random/file', 'another/file', 'empty/message']);
    }
}
