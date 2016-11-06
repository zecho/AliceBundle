# Advanced usage

## Enabling databases

The database management is done in [FidryAliceDataFixtures](https://github.com/theofidry/AliceDataFixtures). Head
to this library documentation for a complete reference.


## Environment specific fixtures

If you wish to use different fixtures depending of the environment, you can easily organise your fixtures the following way:

```
.
└── app/Resources/fixtures/orm/
    ├── environmentless-fixture1.yml
    ├── ...
    ├── inte
    |   ├── prod-fixture1.yml
    |   ├── ...
    └─── dev
            ├── dev-fixture1.yml
            └── ...
```

Then, when you're running the command `php app/console hautelook:fixtures:load --env=inte`, it will load all the
fixtures found in `Resources/fixtures/orm/` (i.e.`environmentless-fixture1.yml`) and in `Resources/fixtures/orm/inte`.


## Fixtures parameters

### Alice parameters

You can already use parameters specifics to your fixture file with
[Alice](https://github.com/nelmio/alice/blob/master/doc/fixtures-refactoring.md#parameters). To manage your fixtures
parameters, you may wish to have a dedicated file for that:

```yaml
# app/Resources/fixtures/orm/parameters.yml

parameters:
    app.alice.parameters.parameter1: something
    app.alice.parameters.parameter2: something else
    ...
```

Then you can use the parameters `app.alice.parameters.parameter1` across all your fixtures files:

```yaml
# app/Resources/fixtures/orm/dummy.yml

AppBundle\Entity\Dummy:
    dummy_0:
        name: <{app.alice.parameters.parameter1}>
```

You can also pass your parameters to functions:

```yaml
# app/Resources/fixtures/orm/dummy.yml

AppBundle\Entity\Dummy:
    dummy_0:
        name: <foo(<{app.alice.parameters.parameter1}>)>
```

For more, check [Alice documentation](https://github.com/nelmio/alice#table-of-contents).


### Application parameters

You can access out of the box to your application parameters:

```yaml
# app/Resources/fixtures/orm/dummy.yml

AppBundle\Entity\Dummy:
    dummy_0:
        locale: <{framework.validation.enabled}>
```

Alice parameters will **not** be injected in your application `ParameterBag`, i.e. are not re-usable outside of the
fixtures.


# Use service factories

If your entity `AppBundle\Entity\Dummy` requires a factory registered as a service (Alice already supports [static
factories](https://github.com/nelmio/alice/blob/master/doc/complete-reference.md#specifying-constructor-arguments)) to
`dummy_factory` be instantiated, you can specify it as a constructor:

```yaml
# app/Resources/fixtures/orm/dummy.yml

AppBundle\Entity\Dummy:
    dummy_0:
        __construct: { '@dummy_factory::create': ['<username()>'] }
```


Previous chapter: [Basic usage](../README.md#basic-usage)<br />
Next chapter: [Custom Faker Providers](faker-providers.md)
