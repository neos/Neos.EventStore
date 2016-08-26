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

## ConcurrencyConflictResolver

Sometimes it's useful to have a fine grained control to solve conflicting event, to avoid triggering a concurrency 
exception when not strictly required or to give user more useful exception messages.

_This is current a work in progress implementation_

### How to declare conflicting events 

By implementing the ```ConflictAwareEventInterface``` like this:

    class ButtonPushed extends AbstractEvent implements ConflictAwareEventInterface
    {
        /**
         * @return array
         */
        public static function conflictsWith(): array
        {
            return [
                ButtonDisabled::class => 'The button has been disabled',
                ButtonRemoved::class => 'The button has been removed',
            ];
        }
    }
    
If the method ```ConflictAwareEventInterface::conflictsWith` returns an emtpy array, the event will never conflicts and 
the store will solve the version concurrency issue automatically.

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
