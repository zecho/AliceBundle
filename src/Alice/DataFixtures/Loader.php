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

use Hautelook\AliceBundle\Alice\DataFixtures\Fixtures\Loader as FixturesLoader;
use Hautelook\AliceBundle\Alice\DataFixtures\Fixtures\LoaderInterface as FixturesLoaderInterface;
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
     * @var FixturesLoaderInterface
     */
    private $fixturesLoader;

    /**
     * @var array|ProcessorInterface[]
     */
    private $processors;

    /**
     * @var bool
     */
    private $persistOnce;

    /**
     * @var int
     */
    private $loadingLimit = 5;

    /**
     * @param FixturesLoaderInterface $fixturesLoader
     * @param ProcessorInterface[]    $processors
     * @param bool                    $persistOnce
     */
    public function __construct(
        FixturesLoaderInterface $fixturesLoader,
        array $processors,
        $persistOnce
    ) {
        $this->fixturesLoader = $fixturesLoader;
        $this->processors = $processors;
        $this->persistOnce = $persistOnce;
    }

    /**
     * Sets load file limit, which is the maximum number of time the loader will try to load the files passed.
     *
     * @param int $loadingLimit
     *
     * @return $this
     */
    public function setLoadingLimit($loadingLimit)
    {
        $this->loadingLimit = $loadingLimit;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function load(PersisterInterface $persister, array $fixturesFiles)
    {
        if ($this->fixturesLoader instanceof FixturesLoader) {
            $_persister = $this->fixturesLoader->getPersister();
            $this->fixturesLoader->setPersister($persister);
        }

        if (0 === count($fixturesFiles)) {
            return [];
        }

        $objects = [];
        $loadFileAttemps = 0;
        $normalizedFixturesFiles = $this->normalizeFixturesFiles($fixturesFiles);

        while(true) {
            $objects = array_merge($objects, $this->tryToLoadFiles($persister, $normalizedFixturesFiles, $objects));

            if (true === $this->areAllFixturesLoaded($normalizedFixturesFiles)) {
                break;
            }

            if ($this->loadingLimit <= $loadFileAttemps) {
                throw new LoadingLimitException($this->loadingLimit, $normalizedFixturesFiles);
            }

            ++$loadFileAttemps;
        }

        if (true === $this->persistOnce) {
            $this->persist($persister, $objects);
        }

        if (isset($_persister)) {
            $this->fixturesLoader->setPersister($_persister);
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

    private function areAllFixturesLoaded(array $normalizedFixturesFiles)
    {
        foreach ($normalizedFixturesFiles as $fileRealPath => $fileHasBeenLoaded) {
            if (false === $fileHasBeenLoaded) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns an array where the key is the fixture file path and the value is the boolean false value.
     *
     * @param string[] $fixturesFiles
     *
     * @return array
     */
    private function normalizeFixturesFiles(array $fixturesFiles)
    {
        $normalizedFixturesFiles = array_flip($fixturesFiles);
        foreach ($normalizedFixturesFiles as $fileRealPath => $index) {
            $normalizedFixturesFiles[$fileRealPath] = false;
        }

        return $normalizedFixturesFiles;
    }

    /**
     * Goes through all fixtures files to try to load them one by one and specify for each if the file could
     * successfuly be loaded or not.
     *
     * @param PersisterInterface $persister
     * @param array              $normalizedFixturesFiles Array with the file real path as key and true as a value if
     *                                                    the files has been loaded.
     * @param array              $references
     *
     * @return \object[] All objects that could have been loaded.
     */
    private function tryToLoadFiles(PersisterInterface $persister, array &$normalizedFixturesFiles, array $references)
    {
        $objects = [];
        foreach ($normalizedFixturesFiles as $fixtureFilePath => $hasBeenLoaded) {
            if (true === $hasBeenLoaded) {
                continue;
            }

            try {
                $dataSet = $this->fixturesLoader->load($fixtureFilePath, $references);
                $normalizedFixturesFiles[$fixtureFilePath] = true;

                if (false === $this->persistOnce) {
                    $this->persist($persister, $dataSet);
                }

                $objects = array_merge($objects, $dataSet);
            } catch (\UnexpectedValueException $exception) {
                // continue
            }
        }

        return $objects;
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
