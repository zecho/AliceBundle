<?php

namespace Hautelook\AliceBundle\Alice\DataFixtures;

use Hautelook\AliceBundle\Alice\ProcessorChain;
use Hautelook\AliceBundle\Faker\Provider\ProviderChain;
use Nelmio\Alice\Fixtures;
use Nelmio\Alice\ProcessorInterface;
use Psr\Log\LoggerInterface;

/**
 * @author Baldur Rensch <brensch@gmail.com>
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class Loader implements LoaderInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var ProcessorInterface[]
     */
    protected $processors;

    /**
     * @param ProcessorChain  $processorChain
     * @param ProviderChain   $providerChain
     * @param string          $locale
     * @param int             $seed
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProcessorChain $processorChain,
        ProviderChain $providerChain,
        $locale,
        $seed,
        LoggerInterface $logger = null
    ) {
        $this->processors = $processorChain->getProcessors();

        $options = [];
        $options['providers'] = $providerChain->getProviders();
        $options['locale'] = $locale;
        $options['seed'] = $seed;
        $options['persist_once'] = false;

        if (null !== $logger) {
            $options['logger'] = $logger;
        }

        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function load($persister, array $fixtures)
    {
        return Fixtures::load(
            $fixtures,
            $persister,
            $this->options,
            $this->processors
        );
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array|ProcessorInterface[]
     */
    public function getProcessors()
    {
        return $this->processors;
    }
}
