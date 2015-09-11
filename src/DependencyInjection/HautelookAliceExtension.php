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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

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
     * {@inheritDoc}
     *
     * @throws InvalidConfigurationException
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config as $key => $value) {
            $container->setParameter($this->getAlias() . '.' .$key, $value);
        }

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        foreach ($config['db_drivers'] as $driver => $isEnabled) {
            if (true === $isEnabled
                || (null === $isEnabled && true === $this->isExtensionEnabled($driver))
            ) {
                $loader->load(sprintf('%s.xml', $driver));
            }
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
