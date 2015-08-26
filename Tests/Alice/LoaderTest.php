<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Tests\Alice;

use Hautelook\AliceBundle\Alice\DataFixtures\Loader;

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
        $processorChain = $this->prophesize('Hautelook\AliceBundle\Alice\ProcessorChain');
        $processorChain->getProcessors()->willReturn(['dummyProcessor']);

        $providerChain = $this->prophesize('Hautelook\AliceBundle\Faker\Provider\ProviderChain');
        $providerChain->getProviders()->willReturn(['dummyProvider']);

        $loader = new Loader($processorChain->reveal(), $providerChain->reveal(), 'en', 10, false);

        $this->assertEquals(['dummyProcessor'], $loader->getProcessors());
        $this->assertEquals(
            [
                'providers'    => ['dummyProvider'],
                'locale'       => 'en',
                'seed'         => 10,
                'persist_once' => false,
            ],
            $loader->getOptions()
        );

        $this->assertEquals(4, count($loader->getOptions()));
        $this->assertEquals(['dummyProvider'], $loader->getOptions()['providers']);
        $this->assertEquals('en', $loader->getOptions()['locale']);
        $this->assertEquals(10, $loader->getOptions()['seed']);
        $this->assertFalse($loader->getOptions()['persist_once']);

        $logger = $this->prophesize('Psr\Log\LoggerInterface')->reveal();

        $loader = new Loader($processorChain->reveal(), $providerChain->reveal(), 'en', 10, false, $logger);

        $this->assertEquals(['dummyProcessor'], $loader->getProcessors());
        $this->assertEquals(
            [
                'providers'    => ['dummyProvider'],
                'locale'       => 'en',
                'seed'         => 10,
                'persist_once' => false,
                'logger'       => $logger,
            ],
            $loader->getOptions()
        );
    }
}
