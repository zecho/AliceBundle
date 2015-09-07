<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Tests\Doctrine\DataFixtures\Executor;

use Hautelook\AliceBundle\Doctrine\DataFixtures\Executor\FixturesExecutor;
use Prophecy\Argument;

/**
 * @coversDefaultClass Hautelook\AliceBundle\Doctrine\DataFixtures\FixturesExecutor
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class FixturesExecutorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @cover ::execute
     * @cover ::getExecutor
     */
    public function testExecuteWithoutTruncate()
    {
        $fixtures = ['fixture1'];

        $objectManagerProphecy = $this->prophesize('Doctrine\Common\Persistence\ObjectManager');

        $eventManagerProphecy = $this->prophesize('Doctrine\Common\EventManager');
        $eventManagerProphecy->addEventSubscriber(Argument::any())->will(
            function ($args) {
                return 'Doctrine\Common\DataFixtures\Event\Listener\ORMReferenceListener' === get_class($args[0]);
            }
        );
        $eventManagerProphecy->addEventSubscriber(Argument::any())->will(
            function ($args) {
                return 'Doctrine\Common\DataFixtures\Event\Listener\MongoDBReferenceListener' === get_class($args[0]);
            }
        );

        $entityManagerProphecy = $this->prophesize('Doctrine\ORM\EntityManagerInterface');
        $entityManagerProphecy->getEventManager()->willReturn($eventManagerProphecy->reveal());
        $entityManagerProphecy->transactional(Argument::any())->shouldBeCalledTimes(1);

        $mongodbDocumentManagerProphecy = $this->prophesize('Doctrine\ODM\MongoDB\DocumentManager');
        $mongodbDocumentManagerProphecy->getEventManager()->willReturn($eventManagerProphecy->reveal());

        $phpcrDocumentManagerProphecy = $this->prophesize('Doctrine\ODM\PHPCR\DocumentManager');

        $loaderProphecy = $this->prophesize('Hautelook\AliceBundle\Alice\DataFixtures\LoaderInterface');
        $loaderProphecy->load(Argument::any(), $fixtures)->will(
            function ($arg1) {
                return 'Nelmio\Alice\Persister\Doctrine' === get_class($arg1[0]);
            }
        );

        $loggerProphecy = $this->prophesize('callable');

        $fixturesExecutor = new FixturesExecutor();

        try {
            $fixturesExecutor->execute(
                $objectManagerProphecy->reveal(),
                $loaderProphecy->reveal(),
                $fixtures,
                $loggerProphecy->reveal(),
                false
            );
            $this->fail('Expected \InvalidArgumentException to be thrown.');
        } catch (\InvalidArgumentException $exception) {
            // Expected result
        }

        $fixturesExecutor->execute(
            $entityManagerProphecy->reveal(),
            $loaderProphecy->reveal(),
            $fixtures,
            $loggerProphecy->reveal(),
            false
        );

        $fixturesExecutor->execute(
            $mongodbDocumentManagerProphecy->reveal(),
            $loaderProphecy->reveal(),
            $fixtures,
            $loggerProphecy->reveal(),
            false
        );

        $fixturesExecutor->execute(
            $phpcrDocumentManagerProphecy->reveal(),
            $loaderProphecy->reveal(),
            $fixtures,
            $loggerProphecy->reveal(),
            false
        );
    }

    /**
     * @cover ::execute
     * @cover ::getExecutor
     */
    public function testExecuteWithTruncate()
    {
        $fixtures = ['fixture1'];

        $objectManagerProphecy = $this->prophesize('Doctrine\Common\Persistence\ObjectManager');

        $eventManagerProphecy = $this->prophesize('Doctrine\Common\EventManager');
        $eventManagerProphecy->addEventSubscriber(Argument::any())->will(
            function ($args) {
                return 'Doctrine\Common\DataFixtures\Event\Listener\ORMReferenceListener' === get_class($args[0]);
            }
        );
        $eventManagerProphecy->addEventSubscriber(Argument::any())->will(
            function ($args) {
                return 'Doctrine\Common\DataFixtures\Event\Listener\MongoDBReferenceListener' === get_class($args[0]);
            }
        );

        $entityManagerProphecy = $this->prophesize('Doctrine\ORM\EntityManagerInterface');
        $entityManagerProphecy->getEventManager()->willReturn($eventManagerProphecy->reveal());
        $entityManagerProphecy->transactional(Argument::any())->shouldBeCalledTimes(1);

        $mongodbDocumentManagerProphecy = $this->prophesize('Doctrine\ODM\MongoDB\DocumentManager');
        $mongodbDocumentManagerProphecy->getEventManager()->willReturn($eventManagerProphecy->reveal());

        $phpcrDocumentManagerProphecy = $this->prophesize('Doctrine\ODM\PHPCR\DocumentManager');

        $loaderProphecy = $this->prophesize('Hautelook\AliceBundle\Alice\DataFixtures\LoaderInterface');
        $loaderProphecy->load(Argument::any(), $fixtures)->will(
            function ($arg1) {
                return 'Nelmio\Alice\Persister\Doctrine' === get_class($arg1[0]);
            }
        );

        $loggerProphecy = $this->prophesize('callable');

        $fixturesExecutor = new FixturesExecutor();

        try {
            $fixturesExecutor->execute(
                $objectManagerProphecy->reveal(),
                $loaderProphecy->reveal(),
                $fixtures,
                $loggerProphecy->reveal(),
                true
            );
            $this->fail('Expected \InvalidArgumentException to be thrown.');
        } catch (\InvalidArgumentException $exception) {
            // Expected result
        }

        $fixturesExecutor->execute(
            $entityManagerProphecy->reveal(),
            $loaderProphecy->reveal(),
            $fixtures,
            $loggerProphecy->reveal(),
            true
        );

        $fixturesExecutor->execute(
            $mongodbDocumentManagerProphecy->reveal(),
            $loaderProphecy->reveal(),
            $fixtures,
            $loggerProphecy->reveal(),
            true
        );

        $fixturesExecutor->execute(
            $phpcrDocumentManagerProphecy->reveal(),
            $loaderProphecy->reveal(),
            $fixtures,
            $loggerProphecy->reveal(),
            true
        );
    }
}
