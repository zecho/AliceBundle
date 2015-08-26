# DoctrineFixturesBundle

This bundle is fully compatible with [DoctrineFixturesBundle][1] although it does not make much sense.

There is a slight incompatibility if you want to migrate from one to another. If you have a data loaders that implement one of the following interface:

* [`Doctrine\Common\DataFixtures\FixtureInterface`](https://github.com/doctrine/data-fixtures/blob/master/lib/Doctrine/Common/DataFixtures/FixtureInterface.php)
* [`Doctrine\Common\DataFixtures\SharedFixtureInterface`](https://github.com/doctrine/data-fixtures/blob/master/lib/Doctrine/Common/DataFixtures/SharedFixtureInterface.php)
* [`Doctrine\Common\DataFixtures\OrderedFixtureInterface`](https://github.com/doctrine/data-fixtures#orderedfixtureinterface)
* [`Doctrine\Common\DataFixtures\DependentFixtureInterface`](https://github.com/doctrine/data-fixtures#dependentfixtureinterface)

The `php app/console h:d:f:l` command will not work very well if you try to import them with `php app/console h:d:f:l`. If
you were simply using data loaders that was implementing [`Doctrine\Common\DataFixtures\FixtureInterface`](https://github.com/doctrine/data-fixtures/blob/master/lib/Doctrine/Common/DataFixtures/FixtureInterface.php) interface, then no issue should be encountered.

This bundle also provides data loaders and they are fully compatible with Doctrine data loaders and will perfectly work with the Doctrine command `php app/console doctrine:fixtures:load`. Beware that in this case, you will have to manually specify the path to your data loaders if you're using [environment specific fixtures](advanced-usage.md#environment-specific-fixtures).

Previous chapter: [Custom Alice Processors](alice-processors.md)<br />
[Back to Table of Contents](../../README.md#documentation)

[1]: https://github.com/doctrine/DoctrineFixturesBundle
