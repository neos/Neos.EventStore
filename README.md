# EventStore infrastucture package for Flow Framework

_This package is currently under development and not fully working, please don't use it in production._

This package is inspired by [LiteCQRS](https://github.com/beberlei/litecqrs-php) and [Broadway](https://github.com/qandidate-labs/broadway).

The goal of the project is to provide infrastructure to support ES (Event Sourcing) project based on Flow Framework

## EventStore

* [x] **EventSerializer**: default implementation, implement your own based on ```EventSerializerInterface```
* [x] **EventSourcedRepository**: abstract class to create an Event Sourced Repository
* [x] **EventStore**: default implementation, implement your own based on ```EventStoreInterface```
* [x] **EventStream**
* [x] **EventStreamData**

## EventStore Implementations

Check external packages:

* [x] **Ttree.EventStore.InMemoryStorageAdapter**: simple testing implementation with not persistence only memory based
* [ ] **Ttree.EventStore.DatabaseStorageAdapter**: Doctrine DBAL implementation

Acknowledgments
---------------

Development sponsored by [ttree ltd - neos solution provider](http://ttree.ch).

We try our best to craft this package with a lots of love, we are open to sponsoring, support request, ... just contact us.

License
-------

Licensed under MIT, see [LICENSE](LICENSE)
