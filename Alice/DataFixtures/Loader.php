<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Alice\DataFixtures;

use Nelmio\Alice\Fixtures\Loader as AliceLoader;
use Nelmio\Alice\PersisterInterface;
use Nelmio\Alice\ProcessorInterface;

/**
 * Bootstraps the given loader to persist the objects retrieved by the loader.
 *
 * @author Baldur Rensch <brensch@gmail.com>
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class Loader implements LoaderInterface
{
    /**
     * @var AliceLoader
     */
    private $aliceLoader;

    /**
     * @var array|ProcessorInterface[]
     */
    private $processors;

    /**
     * @var bool
     */
    private $persistOnce;

    /**
     * @param AliceLoader          $aliceLoader
     * @param ProcessorInterface[] $processors
     * @param bool                 $persistOnce
     */
    public function __construct(
        AliceLoader $aliceLoader,
        array $processors,
        $persistOnce
    ) {
        $this->aliceLoader = $aliceLoader;
        $this->processors = $processors;
        $this->persistOnce = $persistOnce;
    }

    /**
     * {@inheritdoc}
     */
    public function load(PersisterInterface $persister, array $fixtures)
    {
        if (0 === count($fixtures)) {
            return [];
        }

        $objects = [];
        foreach ($fixtures as $file) {
            $dataSet = $this->aliceLoader->load($file);

            if (false === $this->persistOnce) {
                $this->persist($persister, $dataSet);
            }

            $objects = array_merge($objects, $dataSet);
        }

        if (true === $this->persistOnce) {
            $this->persist($persister, $objects);
        }

        return $objects;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistOnce()
    {
        return $this->persistOnce;
    }

    /**
     * Uses the Fixture persister to persist objects and calling the processors.
     *
     * @param PersisterInterface $persister
     * @param object[]           $objects
     */
    private function persist(PersisterInterface $persister, array $objects)
    {
        foreach ($this->processors as $processor) {
            foreach ($objects as $object) {
                $processor->preProcess($object);
            }
        }

        $persister->persist($objects);

        foreach ($this->processors as $processor) {
            foreach ($objects as $object) {
                $processor->postProcess($object);
            }
        }
    }
}
