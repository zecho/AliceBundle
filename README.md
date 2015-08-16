AliceBundle
===========

A [Symfony](symfony.com) bundle to manage fixtures with [nelmio/alice](https://github.com/nelmio/alice) and
[fzaninotto/Faker](https://github.com/fzaninotto/Faker).

[![Build Status](https://travis-ci.org/hautelook/AliceBundle.png?branch=master)](https://travis-ci.org/hautelook/AliceBundle)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/hautelook/AliceBundle/badges/quality-score.png?s=0b9ff0ac44085bc49fdb98f4ea1fec2fea918a39)](https://scrutinizer-ci.com/g/hautelook/AliceBundle/)
[![Code Coverage](https://scrutinizer-ci.com/g/hautelook/AliceBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/hautelook/AliceBundle/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/1169e133-3d02-4ba8-a87e-f152c620f8b5/mini.png)](https://insight.sensiolabs.com/projects/1169e133-3d02-4ba8-a87e-f152c620f8b5)

## Documentation

1. [Install](#install)
2. [Basic usage](#basic-usage)
3. [Advanced usage](Resources/doc/advanced-usage.md)
4. [Custom Faker Providers](Resources/doc/faker-providers.md)
5. [Custom Processors](Resources/doc/alice-processors.md)
6. [Doctrine support](Resources/doc/doctrine.md)

Other references:
* [Knp University screencast](https://knpuniversity.com/screencast/alice-fixtures)

## Installation

You can use [Composer](https://getcomposer.org/) to install the bundle to your project:

```bash
composer require hautelook/alice-bundle
```

Then, enable the bundle by updating your `app/config/AppKernel.php` file to enable the bundle:

```php
<?php
// app/config/AppKernel.php

public function registerBundles()
{
    //...
    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        //...
        $bundles[] = new Hautelook\AliceBundle\HautelookAliceBundle();
    }

    return $bundles;
}
```

Configure the bundle to your needs:

```yaml
# app/config/config.yml

hautelook_alice:
    locale: en_US   # Locale to use with faker; must be a valid Faker locale otherwise will fallback to en_EN
    seed: 1         # A seed to make sure faker generates data consistently across runs, set to null to disable
```

Fore more information regarding the locale, refer to
[Faker documentation on localization](https://github.com/fzaninotto/Faker#localization)

## Basic usage

Assuming you are using [Doctrine](http://www.doctrine-project.org/projects/orm.html), install
the `doctrine/doctrine-bundle` and `doctrine/data-fixtures` packages and register both bundles.
Then create a fixture file in `AppBundle/DataFixtures/ORM`:

```yaml
AppBundle\Entity\Dummy:
    dummy_{1..10}:
        name: <name()>
```

Then simply load your fixtures with the doctrine command `php app/console hautelook_alice:fixtures:load` (or `php app/console h:f:l`).

[See more](#documentation).<br />
Next chapter: [Advanced usage](Resources/doc/advanced-usage.md)


## Credits

This bundle is developped by [Baldur Rensch](https://github.com/baldurrensch), [HauteLook](https://github.com/hautelook)
and its [awesome contributors](https://github.com/hautelook/AliceBundle/graphs/contributors).

## License

[![license](https://img.shields.io/badge/license-MIT-red.svg?style=flat-square)](Resources/meta/LICENSE)
