# Alice Processors

Given you declared a processor `App\DataFixtures\Processor\UserProcessor`, you have to declare it as a
service with the tag `fidry_alice_data_fixtures.processor` to register it:

```yaml
# app/config/services.yml

services:
    data_fixtures.processor.user:
        class: App\DataFixtures\Processor\UserProcessor
        tags: [ { name: fidry_alice_data_fixtures.processor } ]
```

Refer to [FidryAliceDataFixtures](https://github.com/theofidry/AliceDataFixtures/blob/master/doc/processors.md#processors)
for a more advanced documentation.

Previous chapter: [Custom Faker providers](faker-providers.md)<br />
[Back to Table of Contents](../README.md#documentation)
