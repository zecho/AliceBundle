# Advanced usage

## Enabling databases

The bundle configuration for the databases is:

```yaml
hautelook_alice:
    db_drivers:
        orm: ~
        mongodb: ~
        phpcr: ~
```

If a driver value is `~` (which is `null`), the driver is enabled only if the proper extension is enabled. For
instance, if you have only Doctrine ORM enabled, then the drivers for Doctrine ODM and Doctrine PHPCR ODM won't be
enabled.

If you which to explicitly enable or disable a driver, just put its value to `true` or `false`.

## Doctrine ORM

If you were using [DoctrineFixturesBundle](https://github.com/doctrine/DoctrineFixturesBundle), take a look at [DoctrineFixturesBundle supports](doctrine-fixtures-bundle.md).


### Environment specific fixtures

If you wish to use different fixtures depending of the environment, you can easily organise your fixtures the following way:

```
.
└── DataFixtures/ORM
    ├── environmentless-fixture1.yml
    ├── ...
    ├── Inte
    |   ├── prod-fixture1.yml
    |   ├── ...
    └─── Dev
            ├── dev-fixture1.yml
            └── ...
```

Then, when you're running the command `php app/console h:d:f:l --env=inte`, it will load all the fixtures matching the pattern `DataFixtures/ORM/*.yml` and `DataFixtures/ORM/Inte/*.yml`. Don't forget that if you're not specifying the environment, the default environment is used (usually `dev`).


### Using Data loaders

Sometime, you will want to omit some fixtures or use fixtures from another bundle. This can be achieved by using a data loader, which is a class implementing the [`Hautelook\Doctrine\DataFixtures\LoaderInterface`](../../Doctrine/DataFixtures/LoaderInterface.php) or extending [`Hautelook\Doctrine\DataFixtures\AbstractLoader`](../../Doctrine/DataFixtures/AbstractLoader.php). You can then specify the fixtures you wish to use by giving an absolute or relative path or even with the `@Bundlename` notation:

```php
<?php

namespace AppBundle\DataFixtures\ORM\Dev;

use Hautelook\AliceBundle\Doctrine\DataFixtures\AbstractLoader;

class DataLoader extends AbstractLoader
{
    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
        	__DIR__.'/../Prod/brand.yml',
            '@DummyBundle/DataFixtures/ORM/product.yml',
        ];
    }
}
```

**Warning**: when you're putting a data loader in a fixture directory, only the fixtures specified by the data loader will be loaded. But you can use several data loaders in the same directory, they will all be loaded.

## Doctrine ODM and Doctrine PHPCR ODM

The usage is the same as [HautelookAliceBundle with Doctrine ORM](#doctrine-orm) replacing `ORM` namespaces and folders by `ODM` for Doctrine ODM and `PHPCR` for Doctrine PHPCR ODM. The commands are:

* `hautelook_alice:doctrine:mongodb:fixtures:load`
* `hautelook_alice:doctrine:phpcr:fixtures:load`

Previous chapter: [Basic usage](../../README.md#basic-usage)<br />
Next chapter: [Custom Faker Providers](faker-providers.md)
