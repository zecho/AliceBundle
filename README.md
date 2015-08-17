AliceBundle
===========

A [Symfony](http://symfony.com) bundle to manage fixtures with [nelmio/alice](https://github.com/nelmio/alice) and
[fzaninotto/Faker](https://github.com/fzaninotto/Faker).

[![Package version](http://img.shields.io/packagist/v/theofidry/alice-bundle.svg?style=flat-square)](https://packagist.org/packages/theofidry/alice-bundle)
[![Build Status](https://img.shields.io/travis/theofidry/AliceBundle.svg?branch=dev&style=flat-square)](https://travis-ci.org/theofidry/AliceBundle?branch=dev)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/d93a3fc4-3fe8-4be3-aa62-307f53898199.svg?style=flat-square)](https://insight.sensiolabs.com/projects/d93a3fc4-3fe8-4be3-aa62-307f53898199)
[![Dependency Status](https://www.versioneye.com/user/projects/55d2221f265ff6001a000001/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55d2221f265ff6001a000001)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/theofidry/AliceBundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/theofidry/AliceBundle/?branch=dev)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/theofidry/AliceBundle.svg?b=dev&style=flat-square)](https://scrutinizer-ci.com/g/theofidry/AliceBundle/?branch=dev)


## Documentation

1. [Install](#installation)
2. [Basic usage](#basic-usage)
3. [Advanced usage](Resources/doc/advanced-usage.md)
4. [Custom Faker Providers](Resources/doc/faker-providers.md)
5. [Custom Alice Processors](Resources/doc/alice-processors.md)
6. [DoctrineFixturesBundle support](Resources/doc/doctrine-fixtures-bundle.md)
7. [Resources](#resources)

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
    locale: en_US   # Locale to used for faker; must be a valid Faker locale otherwise will fallback to en_EN
    seed: 1         # A seed to make sure faker generates data consistently across runs, set to null to disable
```

Fore more information regarding the locale, refer to
[Faker documentation on localization](https://github.com/fzaninotto/Faker#localization).

## Basic usage

Assuming you are using [Doctrine](http://www.doctrine-project.org/projects/orm.html), install
the [`doctrine/doctrine-bundle`](https://github.com/doctrine/DoctrineBundle) and [`doctrine/data-fixtures`](https://github.com/doctrine/data-fixtures) packages and register both bundles.
Then create a fixture file in `AppBundle/DataFixtures/ORM`:

```yaml
AppBundle\Entity\Dummy:
    dummy_{1..10}:
        name: <name()>
```

Then simply load your fixtures with the doctrine command `php app/console hautelook_alice:fixtures:load` (or `php app/console h:f:l`).

If you want to load the fixtures of a bundle only, do `php app/console h:f:l -b MyFirstBundle -b MySecondBundle`.

[See more](#documentation).<br />
Next chapter: [Advanced usage](Resources/doc/advanced-usage.md)


## Resources

* [Upgrade guide](UPGRADE.md)
  * [Upgrade from 0.X to 1.X](UPGRADE.md#from-0x-to-1x)
* [Changelog](CHANGELOG.md)

## Credits

This bundle is developped by [Baldur Rensch](https://github.com/baldurrensch), [HauteLook](https://github.com/hautelook)
and its [awesome contributors](https://github.com/hautelook/AliceBundle/graphs/contributors).

## License

[![license](https://img.shields.io/badge/license-MIT-red.svg?style=flat-square)](Resources/meta/LICENSE)
