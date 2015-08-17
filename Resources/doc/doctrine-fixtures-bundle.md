# [DoctrineFixturesBundle][1]

This bundle is fully compatible with [DoctrineFixturesBundle][1]. However, it does not make much sense to use both of them together and using data loaders that implement one of the following interface:

* [`LoaderInterface`](https://github.com/doctrine/data-fixtures/blob/master/lib/Doctrine/Common/DataFixtures/LoaderInterface.php)
* [`SharedFixtureInterface`](https://github.com/doctrine/data-fixtures/blob/master/lib/Doctrine/Common/DataFixtures/SharedFixtureInterface.php)
* [`OrderedFixtureInterface`](https://github.com/doctrine/data-fixtures#orderedfixtureinterface)
* [`DependentFixtureInterface`](https://github.com/doctrine/data-fixtures#orderedfixtureinterface)

won't work very well if you try to import them with `php app/console h:f:l`. If you were simply using data loaders that was implementing `Doctrine\Common\DataFixtures\FixtureInterface` interface, then no problem should be encontered.

This bundle also provides data loaders and they are fully compatible with Doctrine data loaders and will perfectly work with the Doctrine command `php app/console doctrine:fixtures:load`. Beware that you will have to manually specify the path to your data loaders if you're using [environment specific fixtures](advanced-usage.md#environment-specific-fixtures).

[Back to Table of Contents](../../README.md#documentation)<br />
Previous chapter: [Custom Processors](alice-processors.md)

[1]: https://github.com/doctrine/DoctrineFixturesBundle
