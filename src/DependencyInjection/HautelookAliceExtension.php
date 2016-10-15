<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * The extension of this bundle.
 *
 * @author Baldur Rensch <brensch@gmail.com>
 */
class HautelookAliceExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var array
     */
    private $extensions = [];

    /**
     * {@inheritdoc}
     *
     * Gets Doctrine extensions.
     */
    public function prepend(ContainerBuilder $container)
    {
        $this->extensions[Configuration::ORM_DRIVER] = $container->getExtensionConfig('doctrine');
        $this->extensions[Configuration::MONGODB_DRIVER] = $container->getExtensionConfig('doctrine_mongodb');
        $this->extensions[Configuration::PHPCR_DRIVER] = $container->getExtensionConfig('doctrine_phpcr');
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config as $key => $value) {
            $container->setParameter($this->getAlias().'.'.$key, $value);
        }

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        // Ensure seed is either an integer or null.
        if (!(is_int($config['seed']) || is_null($config['seed']))) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expected the seed argument to be an integer or null. Got an argument of type %s instead.',
                    gettype($config['seed'])
                )
            );
        }

        // Deprecated factory methods handling.
        // To be removed and set directly on config file when bumping Symfony requirements to >=2.6
        $aliceFakerDefinition = $container->getDefinition('hautelook_alice.faker');
        if (method_exists($aliceFakerDefinition, 'setFactory')) {
            $aliceFakerDefinition->setFactory(['Faker\Factory', 'create']);
        } else {
            $aliceFakerDefinition->setFactoryClass('Faker\Factory');
            $aliceFakerDefinition->setFactoryMethod('create');
        }

        foreach ($config['db_drivers'] as $driver => $isEnabled) {
            if (true === $isEnabled
                || (null === $isEnabled && true === $this->isExtensionEnabled($driver))
            ) {
                $loader->load(sprintf('%s.xml', $driver));

                if ('orm' === $driver) {
                    $this->setCommandFactory($container->getDefinition('hautelook_alice.doctrine.command.deprecated_load_command'));
                    $this->setCommandFactory($container->getDefinition('hautelook_alice.doctrine.command.load_command'));
                } else {
                    $this->setCommandFactory($container->getDefinition(sprintf('hautelook_alice.doctrine.%s.command.load_command', $driver)));
                }
            }
        }

        $container->getDefinition('hautelook_alice.alice.fixtures.loader')
            ->replaceArgument(3, $container->getParameterBag()->all());
    }

    private function setCommandFactory(Definition $commandDefinition)
    {
        // Deprecated factory methods handling.
        // To be removed and set directly on config file when bumping Symfony requirements to >=2.6
        if (method_exists($commandDefinition, 'setFactory')) {
            $commandDefinition->setFactory([new Reference('hautelook_alice.doctrine.command_factory'), 'createCommand']);
        } else {
            $commandDefinition->setFactoryService('hautelook_alice.doctrine.command_factory');
            $commandDefinition->setFactoryMethod('createCommand');
        }
    }

    /**
     * @param string $driver
     *
     * @return bool
     */
    private function isExtensionEnabled($driver)
    {
        return !empty($this->extensions[$driver]);
    }
}
