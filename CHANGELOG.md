# WIP

* Upgraded [nelmio/alice](https://github.com/nelmio/alice) to the version [2.0.0](https://github.com/nelmio/alice/releases/tag/2.0.0)
* Now uses [`Nelmio\Alice\Fixtures`](https://github.com/nelmio/alice/blob/master/src/Nelmio/Alice/Fixtures.php) and
its loaders instead of custom ones
* Added configuration parameter [`hautelook_alice.logger`](DependencyInjection/Configuration.php#L28-L31) to pass a
logger
* Now possible to register [Alice Processors][1]
* Now possible to register [Custom Faker Data Providers][2]
* Enhanced documentation

[1]: https://github.com/nelmio/alice#processors
[2]: https://github.com/nelmio/alice#custom-faker-data-providers
