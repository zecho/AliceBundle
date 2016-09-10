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
use Hautelook\AliceBundle\Alice\ProcessorChain;
use Nelmio\Alice\PersisterInterface;

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
     * @var ProcessorChain
     */
    private $processorChain;

    /**
     * @var bool
     */
    private $persistOnce;

    /**
     * @var int
     */
    private $loadingLimit;

    /**
     * @var string[]
     */
    private $errorMessages;

    /**
     * @param FixturesLoaderInterface $fixturesLoader
     * @param ProcessorChain          $processorChain
     * @param bool                    $persistOnce
     * @param int                     $loadingLimit
     */
    public function __construct(
        FixturesLoaderInterface $fixturesLoader,
        ProcessorChain $processorChain,
        $persistOnce,
        $loadingLimit
    ) {
        $this->fixturesLoader = $fixturesLoader;
        $this->processorChain = $processorChain;
        $this->persistOnce = $persistOnce;
        $this->loadingLimit = $loadingLimit;
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
        $loadFileAttempts = 0;
        $normalizedFixturesFiles = $this->normalizeFixturesFiles($fixturesFiles);

        $this->errorMessages = [];
        while (true) {
            $objects = $this->tryToLoadFiles($persister, $normalizedFixturesFiles, $objects);

            if (true === $this->areAllFixturesLoaded($normalizedFixturesFiles)) {
                break;
            }

            if ($this->loadingLimit <= $loadFileAttempts) {
                throw new LoadingLimitException($this->loadingLimit, $normalizedFixturesFiles, $this->errorMessages);
            }

            ++$loadFileAttempts;
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
     * @return ProcessorChain
     */
    public function getProcessorChain()
    {
        return $this->processorChain;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessors()
    {
        return $this->getProcessorChain()->getProcessors();
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistOnce()
    {
        return $this->persistOnce;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoadingLimit()
    {
        return $this->loadingLimit;
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

                $references = array_merge($references, $dataSet);
            } catch (\UnexpectedValueException $exception) {
                $message = $exception->getMessage();
                if (1 !== preg_match(
                        '/Instance .* is not defined/',
                        $message
                    )
                    && 1 !== preg_match(
                        '/Instance mask .* did not match any existing instance/',
                        $message
                    )
                ) {
                    throw $exception;
                }

                $this->registerErrorMessage($fixtureFilePath, $message);
            }
        }

        return $references;
    }

    /**
     * Uses the Fixture persister to persist objects and calling the processors.
     *
     * @param PersisterInterface $persister
     * @param object[]           $objects
     */
    private function persist(PersisterInterface $persister, array $objects)
    {
        foreach ($this->getProcessors() as $processor) {
            foreach ($objects as $object) {
                $processor->preProcess($object);
            }
        }

        $persister->persist($objects);

        foreach ($this->getProcessors() as $processor) {
            foreach ($objects as $object) {
                $processor->postProcess($object);
            }
        }
    }

    /**
     * Registers the error message with the related fixture file.
     *
     * @param string $fixtureFilePath
     * @param string $errorMessage
     */
    private function registerErrorMessage($fixtureFilePath, $errorMessage)
    {
        if (true === empty($errorMessage)) {
            return;
        }

        if (!isset($this->errorMessages[$fixtureFilePath])) {
            $this->errorMessages[$fixtureFilePath] = [];
        }

        array_push($this->errorMessages[$fixtureFilePath], $errorMessage);
    }
}
