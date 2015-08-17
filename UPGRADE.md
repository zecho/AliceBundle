# Upgrading guide

## From 0.X to 1.X

### Upgrading the data loaders

1. You data loader should now either extend [`Hautelook\Doctrine\DataFixtures\AbstractLoader`](Doctrine/DataFixtures/AbstractLoader.php) or implement [`Hautelook\Doctrine\DataFixtures\LoaderInterface`](Doctrine/DataFixtures/LoaderInterface.php).

2. If you were overriding the `::load()` function of the data loader, you should not need it anymore now:
  * Custom Faker providers can now be registered, cf [Custom Faker Providers](Resources/doc/faker-providers.md).
  * Custom Alice processors can now be registered, cf [Custom Processors](Resources/doc/alice-processors.md).

3. If you had very long path for some fixtures because you needed to refer to the fixtures of another bundle, you can now use the bundle annotation `@Bundlename`.

4. If you had several data loaders to manage different set of fixtures depending of your environment, now you can [devide your fixtures by environment](Resources/doc/advanced-usage.md#environment-specific-fixtures) instead of having to use and specify a data loader for that.


### Doctrine command

You should now rely on the bundle command `hautelook_alice:fixtures:load` (or `h:f:l`) instead of `doctrine:fixtures:load`.


### Remove DoctrineFixturesBundle

As explained [here](Resources/doc/doctrine-fixtures-bundle.md), there is no obligation to do so. HautelookAliceBundle is fully compatible with it. However it does not make sense to use the both of them together. It is recommended to
choose only one.

[Back to the documentation](README.md)
