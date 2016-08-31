# EventStore for Flow Framework project

_This package is currently under development and not fully working, please don't use it in production._

Check for more documentation [Neos.Cqrs](https://github.com/neos/Neos.Cqrs)**: mostly infrastructure (interface, trait, abstract class) and the event/query bus

The goal of the project is to provide infrastructure to support ES (Event Sourcing) project based on Flow Framework

## EventStore

* [x] **EventSerializer**: default implementation, implement your own based on ```EventSerializerInterface```
* [x] **EventSourcedRepository**: abstract class to create an Event Sourced Repository
* [x] **EventStore**: default implementation, implement your own based on ```EventStoreInterface```
* [x] **EventStream**
* [x] **EventStreamData**

## EventStore Implementations

Check external packages:

* [x] **[Neos.EventStore.InMemoryStorageAdapter](https://github.com/dfeyer/Neos.EventStore.InMemoryStorageAdapter)**: simple testing implementation with not persistence only memory based
* [x] **[Neos.EventStore.DatabaseStorageAdapter](https://github.com/dfeyer/Neos.EventStore.DatabaseStorageAdapter)**: Doctrine DBAL implementation

License
-------

Licensed under MIT, see [LICENSE](LICENSE)
