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
use Hautelook\AliceBundle\Tests\KernelTestCase;
use Nelmio\Alice\PersisterInterface;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class LoaderFunctionalTest extends KernelTestCase
{
    /**
     * @var Loader
     */
    private $loader;

    protected function setUp()
    {
        self::bootKernel();

        $this->loader = self::$kernel->getContainer()->get('hautelook_alice.fixtures.loader');
    }

    /**
     * @cover ::load
     * @cover ::persist
     */
    public function testLoadTrickyFixtures()
    {
        $files = [
            __DIR__.'/tricky_fixtures/strength.yml',
            __DIR__.'/tricky_fixtures/project.yml',
            __DIR__.'/tricky_fixtures/city.yml',
        ];

        $this->loader->load(new FakePersister(), $files);
    }
}

class FakePersister implements PersisterInterface
{
    public $objects = [];

    public function persist(array $objects)
    {
    }

    public function find($class, $id)
    {
    }
}
