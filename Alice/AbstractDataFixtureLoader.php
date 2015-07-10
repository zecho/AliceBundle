<?php

namespace Hautelook\AliceBundle\Alice;

use Nelmio\Alice\Fixtures;
use Nelmio\Alice\ProcessorInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Baldur Rensch <brensch@gmail.com>
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
abstract class AbstractDataFixtureLoader implements ContainerAwareInterface
{
    /**
     * @var ProcessorInterface[]
     */
    protected $processors;

    /**
     * @var array
     */
    protected $options;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        if (null === $container) {
            return;
        }

        $this->processors = $container->get('hautelook_alice.processor_chain')->getProcessors();

        $options = [];
        $options['providers'] = $container->get('hautelook_alice.faker.provider_chain')->getProviders();
        $options['locale'] = $container->getParameter('hautelook_alice.locale');
        $options['seed'] = $container->getParameter('hautelook_alice.seed');
        $options['persist_once'] = false;

        $loggerId = $container->getParameter('hautelook_alice.logger');
        if ($container->has($loggerId)) {
            $options['logger'] = $container->get($loggerId);
        }

        $this->options = $options;
    }

    /**
     * Loads the fixtures files
     *
     * @param object $persister
     *
     * @return array
     */
    public function loadFixtures($persister)
    {
        return Fixtures::load($this->getFixtures(),
            $persister,
            $this->options,
            $this->processors);
    }

    /**
     * Returns an array of file paths to fixtures
     *
     * @return string[]
     */
    abstract protected function getFixtures();

    /**
     * @return string Fixture loader ID
     */
    protected function getLoaderId()
    {
        return 'hautelook_alice.fixtures.loader';
    }
}
