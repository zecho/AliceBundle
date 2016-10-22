<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Doctrine\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Helper for declaring doctrine data loaders.
 *
 * @author Baldur Rensch <brensch@gmail.com>
 * @author Théo FIDRY <theo.fidry@gmail.com>
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
abstract class AbstractLoader implements ContainerAwareInterface, LoaderInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct()
    {
        @trigger_error(
            'Doctrine loaders support is deprecated since 1.4.0 and will be removed in 2.0.',
            E_USER_DEPRECATED
        );
    }


    /**
     * {@inheritdoc}
     *
     * Use Symfony\Component\DependencyInjection\ContainerAwareTrait instead when dropping SF 2.3 and PHP 5.3 support.
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Loads the fixtures files.
     *
     * @param ObjectManager $objectManager
     *
     * @return \object[] Persisted objects
     */
    public function load(ObjectManager $objectManager)
    {
    }
}
