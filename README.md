AliceBundle
===========

A [Symfony](http://symfony.com) bundle to manage fixtures with [nelmio/alice](https://github.com/nelmio/alice) and
[fzaninotto/Faker](https://github.com/fzaninotto/Faker).

Currently supports [Doctrine ORM](http://www.doctrine-project.org/projects/orm.html) and [Doctrine ODM](http://doctrine-mongodb-odm.readthedocs.org/en/latest/).

[![Package version](https://img.shields.io/packagist/v/hautelook/alice-bundle.svg?style=flat-square)](https://packagist.org/packages/hautelook/alice-bundle)
[![Build Status](https://img.shields.io/travis/hautelook/AliceBundle/1.x.svg?style=flat-square)](https://travis-ci.org/hautelook/AliceBundle?branch=1.x)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/d93a3fc4-3fe8-4be3-aa62-307f53898199.svg?style=flat-square)](https://insight.sensiolabs.com/projects/d93a3fc4-3fe8-4be3-aa62-307f53898199)
[![Dependency Status](https://www.versioneye.com/user/projects/55d26478265ff6001a000084/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55d26478265ff6001a000084)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/hautelook/AliceBundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/hautelook/AliceBundle/?branch=1.x)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/hautelook/AliceBundle.svg?b=1.x&style=flat-square)](https://scrutinizer-ci.com/g/hautelook/AliceBundle/?branch=1.x)

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/hautelook/AliceBundle?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

## Documentation

1. [Install](#installation)
2. [Basic usage](#basic-usage)
3. [Advanced usage](src/Resources/doc/advanced-usage.md)
    1. [Enabling databases](src/Resources/doc/advanced-usage.md#enabling-databases)
    2. [Fixtures parameters](src/Resources/doc/advanced-usage.md#fixtures-parameters)
    3. [Doctrine ORM](src/Resources/doc/advanced-usage.md#doctrine-orm)
    4. [Doctrine ODM (MongoDB)](src/Resources/doc/advanced-usage.md#doctrine-odm-and-doctrine-phpcr-odm)
    5. [Doctrine PHPCR ODM](src/Resources/doc/advanced-usage.md#doctrine-odm-and-doctrine-phpcr-odm)
4. [Custom Faker Providers](src/Resources/doc/faker-providers.md)
    1. [Simple Provider](src/Resources/doc/faker-providers.md#simple-provider)
    2. [Advanced Provider](src/Resources/doc/faker-providers.md#advanced-provider)
5. [Custom Alice Processors](src/Resources/doc/alice-processors.md)
6. [DoctrineFixturesBundle support](src/Resources/doc/doctrine-fixtures-bundle.md)
7. [Resources](#resources)

Other references:

* [Knp University screencast](https://knpuniversity.com/screencast/alice-fixtures)

## Installation

First you need install appropriate database managers (if you didn't install it yet), according to your project requirements.
Check the documentation [here](src/Resources/doc/install.md).

You can use [Composer](https://getcomposer.org/) to install the bundle to your project:

```bash
composer require --dev hautelook/alice-bundle doctrine/data-fixtures
```

Of course, the `doctrine/data-fixtures` library is only needed if you're using Doctrine.

Then, enable the bundle by updating your `app/AppKernel.php` file to enable the bundle:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    //...
    if (in_array($this->getEnvironment(), ['dev', 'test'])) {
        //...
        $bundles[] = new Hautelook\AliceBundle\HautelookAliceBundle();
    }

    return $bundles;
}
```

Configure the bundle to your needs (example with default values):

```yaml
# app/config/config_dev.yml

hautelook_alice:
    db_drivers:
        orm: ~          # Enable Doctrine ORM if is registered
        mongodb: ~      # Enable Doctrine ODM if is registered
        phpcr: ~        # Enable Doctrine PHPCR ODM if is registered
    locale: en_US       # Locale to used for faker; must be a valid Faker locale otherwise will fallback to en_US
    seed: 1             # A seed to make sure faker generates data consistently across runs, set to null to disable
    persist_once: false # Only persist objects once if multiple files are passed
    loading_limit: 5    # Maximum number of time the loader will try to load the files passed
```

Fore more information regarding the locale, refer to
[Faker documentation on localization](https://github.com/fzaninotto/Faker#localization).

## Basic usage

Assuming you are using [Doctrine](http://www.doctrine-project.org/projects/orm.html), make sure you
have the [`doctrine/doctrine-bundle`](https://github.com/doctrine/DoctrineBundle) and [`doctrine/data-fixtures`](https://github.com/doctrine/data-fixtures) packages installed.

Then create a fixture file in `src/AppBundle/DataFixtures/ORM`:

```yaml
# src/AppBundle/DataFixtures/ORM/dummy.yml

AppBundle\Entity\Dummy:
    dummy_{1..10}:
        name: <name()>
        related_dummy: '@related_dummy*'
```

```yaml
# AppBundle/DataFixtures/ORM/related_dummy.yml

AppBundle\Entity\RelatedDummy:
    related_dummy_{1..10}:
        name: <name()>
```

Then simply load your fixtures with the doctrine command `php app/console fixtures:load`.

If you want to load the fixtures of a bundle only, do `php app/console fixtures:load -b MyFirstBundle -b MySecondBundle`.

[See more](#documentation).<br />
Next chapter: [Advanced usage](src/Resources/doc/advanced-usage.md)


## Resources

* Behat extension: [AliceBundleExtension](https://github.com/theofidry/AliceBundleExtension)
* Bundle for generating AliceBundle compatible fixtures directly from Doctrine entities: [AliceGeneratorBundle](https://github.com/trappar/AliceGeneratorBundle)
* [Upgrade guide](UPGRADE.md)
  * [Upgrade from 0.X to 1.X](UPGRADE.md#from-0x-to-1x)
* [Changelog](CHANGELOG.md)

## Credits

This bundle was originaly developped by [Baldur RENSCH](https://github.com/baldurrensch) and [HauteLook](https://github.com/hautelook). It is now maintained by [Théo FIDRY](https://github.com/theofidry).

[Other contributors](https://github.com/hautelook/AliceBundle/graphs/contributors).

## License

[![license](https://img.shields.io/badge/license-MIT-red.svg?style=flat-square)](Resources/meta/LICENSE)
