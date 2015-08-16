# Processors

Refer to [nelmio/alice](https://github.com/nelmio/alice/blob/master/doc/processors.md#processors) documentation to see how to create a Processor
class. Given you declared a processor `AppBundle\DataFixtures\Processor\UserProcessor`, you have to declared it as a
service with the tag `hautelook_alice.processor` to register it:

```yaml
# app/config/services.yml

services:
    alice.processor.user:
        class: AppBundle\DataFixtures\Processor\UserProcessor
        tags: [ { name: hautelook_alice.processor } ]
```

Next chapter: [Doctrine support](doctrine.md)<br />
Previous chapter: [Custom Faker providers](faker-providers.md)
