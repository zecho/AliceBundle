# Upgrade from 0.X to 1.X

## Upgrading the data loaders

1. You data loader should now either extend `Hautelook\Doctrine\DataFixtures\AbstractLoader` or implement `Hautelook\Doctrine\DataFixtures\LoaderInterface`.

2. If you were overriding the `::load()` of the data loader, you should not need it anymore now:
* Custom Faker providers can not be registered, cf [Custom Faker Providers](Resources/doc/faker-providers.md)
* Custom Alice processors can not be registered, cf [Custom Processors](Resources/doc/alice-processors.md).

3. If you had very long path for some fixtures because you needed to refer to the fixtures of another bundle, you can now use the bundle annotation `@Bundlename`.

4. If you had several data loaders to manage different set of fixtures depending of your environment, now you can [devide your fixtures by environment](Resources/doc/advanced-usage.md#environment-specific-fixtures) instead of having to use and specify a data loader for that.


## Doctrine command

You should now rely on the bundle command `php app/console hautelook_alice:fixtures:load` (or `php app/console h:f:l`) instead of `php app/console doctrine:fixtures:load`.


## Remove DoctrineFixturesBundle

As explained [here](Resources/doc/doctrine-fixtures-bundle.md), there is no obligation and HautelookAliceBundle is fully compatible with it. However it does not make sense to use both together to it is recommanded to choice between the both of them.