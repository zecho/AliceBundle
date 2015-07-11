# Doctrine support

To use [Doctrine](http://www.doctrine-project.org/projects/orm.html) with your Symfony application, you have to make
sur you installed [DoctrineBundle](https://github.com/doctrine/DoctrineBundle) first.

This bundle is fully compatible with [DoctrineFixturesBundle](https://github.com/doctrine/DoctrineFixturesBundle) which
is a Symfony port of [doctrine/data-fixtures](https://github.com/doctrine/data-fixtures).

To use it, simply declare as usual your [`DataFixtureLoader`](https://github.com/doctrine/data-fixtures#doctrine-data-fixtures-extension)
which implements one of the following interface:

* [`FixtureInterface`](https://github.com/doctrine/data-fixtures/blob/master/lib/Doctrine/Common/DataFixtures/FixtureInterface.php)
* [`SharedFixtureInterface`](https://github.com/doctrine/data-fixtures/blob/master/lib/Doctrine/Common/DataFixtures/SharedFixtureInterface.php)
* [`OrderedFixtureInterface`](https://github.com/doctrine/data-fixtures#orderedfixtureinterface)
* [`DependentFixtureInterface`](https://github.com/doctrine/data-fixtures#orderedfixtureinterface)

Then to use the features provided by this bundle, simply extend the
[`Hautelook\AliceBundle\Doctrine\DataFixtures\AbstractDataFixtureLoader`](Doctrine/DataFixtures/AbstractDataFixtureLoader)
class:

```php
<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Hautelook\AliceBundle\Doctrine\DataFixtures\AbstractDataFixtureLoader;

class BrandFixtureLoader extends AbstractDataFixtureLoader
{
    /**
     * {@inheritdoc}
     */
    protected function getFixtures()
    {
        return [
            __DIR__.'/brand.yml',
            __DIR__.'/product.yml',
        ];
    }
}
```

The to load them you can use the doctrine command `doctrine:fixtures:load`.

### Warning

There is currently a known bug which prevents to use the
[`OrderedFixtureInterface`](https://github.com/doctrine/data-fixtures#orderedfixtureinterface) and
[`DependentFixtureInterface`](https://github.com/doctrine/data-fixtures#orderedfixtureinterface). See #13 for more
details.

Next chapter: [Back to Table of Contents](../../README.md#documentation)<br />
Previous chapter: [Custom Processors](processors.md)
