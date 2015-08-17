# Advanced usage

For now, this bundle works only with [Doctrine](http://www.doctrine-project.org/projects/orm.html) and [doctrine/data-fixtures](https://github.com/doctrine/data-fixtures).

If you were using [DoctrineFixturesBundle](https://github.com/doctrine/DoctrineFixturesBundle), take a look at [DoctrineFixturesBundle supports](doctrine-fixtures-bundle.md).


## Environment specific fixtures

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

Then, when you're running the command `php app/console h:f:l --env=inte`, it will load all the fixtures matching the pattern `DataFixtures/ORM/*.yml` and `DataFixtures/ORM/Inte/*.yml`. Don't forget that if you're not specifying the environment, the default environment is used (usually `dev`).


## Using Data loaders

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

Previous chapter: [Basic usage](../../README.md#basic-usage)<br />
Next chapter: [Custom Faker Providers](faker-providers.md)
