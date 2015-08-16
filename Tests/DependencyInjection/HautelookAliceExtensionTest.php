<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Tests\DependencyInjection;

use Hautelook\AliceBundle\DependencyInjection\HautelookAliceExtension;
use Prophecy\Argument;

/**
 * @coversDefaultClass Hautelook\AliceBundle\DependencyInjection\HautelookAliceExtension
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class HautelookAliceExtensionTest extends \PHPUnit_Framework_TestCase
{
    private static $defaultConfig = [
        'hautelook_alice' => [
            'locale' => 'en_US',
            'seed'   => 1,
        ],
    ];

    /**
     * @cover ::__construct
     */
    public function testConstruct()
    {
        $extension = new HautelookAliceExtension();
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Extension\ExtensionInterface', $extension);
        $this->assertInstanceOf(
            'Symfony\Component\DependencyInjection\Extension\ConfigurationExtensionInterface',
            $extension
        );
    }
}
