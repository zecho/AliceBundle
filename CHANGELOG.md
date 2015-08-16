# Version 1.X

* Upgraded [nelmio/alice](https://github.com/nelmio/alice) to the version [2.0.0](https://github.com/nelmio/alice/releases/tag/2.0.0)
* Now uses [`Nelmio\Alice\Fixtures`](https://github.com/nelmio/alice/blob/master/src/Nelmio/Alice/Fixtures.php) and
its loaders instead of custom ones
* Introduced a command `hautelook_alice:fixtures:load`
* Possibility to load fixtures by environment
* Possibility to load fixtures by bundle
* No longer need data loader to load fixtures
* Removed DoctrineFixturesBundle](https://github.com/doctrine/DoctrineFixturesBundle) dependency
* Now possible to register [Alice Processors][1]
* Now possible to register [Custom Faker Data Providers][2]
* Enhanced documentation

[1]: https://github.com/nelmio/alice#processors
[2]: https://github.com/nelmio/alice#custom-faker-data-providers
