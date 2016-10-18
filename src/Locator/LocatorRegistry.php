<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Locator;

use Hautelook\AliceBundle\FixtureLocatorInterface;
use Nelmio\Alice\NotClonableTrait;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class LocatorRegistry implements FixtureLocatorInterface
{
    use NotClonableTrait;

    /**
     * @var FixtureLocatorInterface[]
     */
    private $locators;

    /**
     * @param FixtureLocatorInterface[] $locators
     */
    public function __construct(array $locators)
    {
        $this->locators = (function (FixtureLocatorInterface ...$locators) { return $locators; })(...$locators);
    }

    /**
     * Locate fixture files found matching the environment name.
     *
     * For example, if the given fixture path is 'Resources/fixtures', it will try to locate
     * the files in the 'Resources/fixtures/*.dev.yml' for each bundle passed ('dev' being the
     * environment).
     *
     * {@inheritdoc}
     */
    public function locateFiles(array $bundles, string $environment): array
    {
        $fixtureFiles = [];
        foreach ($this->locators as $locator) {
            $files = array_flip($locator->locateFiles($bundles, $environment));
            $fixtureFiles = $fixtureFiles + $files;
        }

        return array_keys($fixtureFiles);
    }
}
