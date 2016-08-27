<?php
namespace Ttree\EventStore;

/*
 * This file is part of the Ttree.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Ttree\Cqrs\Domain\AggregateRootInterface;
use Ttree\Cqrs\Domain\Exception\AggregateRootNotFoundException;
use Ttree\Cqrs\Domain\RepositoryInterface;
use Ttree\Cqrs\Event\EventBusInterface;
use Ttree\Cqrs\Event\EventInterface;
use Ttree\EventStore\Domain\EventSourcedAggregateRootInterface;
use Ttree\EventStore\Exception\EventStreamNotFoundException;
use TYPO3\Flow\Annotations as Flow;

/**
 * EventSourcedRepository
 */
abstract class EventSourcedRepository implements RepositoryInterface
{
    /**
     * @var EventStoreInterface
     * @Flow\Inject
     */
    protected $eventStore;

    /**
     * @var EventBusInterface
     * @Flow\Inject
     */
    protected $eventBus;

    /**
     * @param string $identifier
     * @return AggregateRootInterface
     * @throws AggregateRootNotFoundException
     */
    public function findByIdentifier($identifier): AggregateRootInterface
    {
        try {
            /** @var EventStream $eventStream */
            $eventStream = $this->eventStore->get($identifier);
        } catch (EventStreamNotFoundException $e) {
            throw new AggregateRootNotFoundException(sprintf(
                "AggregateRoot with id '%s' not found", $identifier
            ), 1471077948);
        }

        // todo don't use the reflexion directly
        $reflection = new \ReflectionClass($eventStream->getAggregateName());

        /** @var EventSourcedAggregateRootInterface $aggregateRoot */
        $aggregateRoot = $reflection->newInstanceWithoutConstructor();
        $aggregateRoot->reconstituteFromEventStream($eventStream);

        return $aggregateRoot;
    }

    /**
     * @param  AggregateRootInterface $aggregate
     * @return void
     */
    public function save(AggregateRootInterface $aggregate)
    {
        try {
            $stream = $this->eventStore
                ->get($aggregate->getAggregateIdentifier());
        } catch (EventStreamNotFoundException $e) {
            $stream = new EventStream(
                $aggregate->getAggregateIdentifier(),
                get_class($aggregate),
                []
            );
        } finally {
            $uncommitedEvents = $aggregate->pullUncommittedEvents();
            $stream->addEvents($uncommitedEvents);
        }

        $this->eventStore->commit($stream);

        /** @var EventInterface $event */
        foreach ($uncommitedEvents as $event) {
            $this->eventBus->handle($event);
        }
    }

    /**
     * @param string $identifier
     * @return boolean
     */
    public function contains($identifier): bool
    {
        return $this->eventStore->contains($identifier);
    }
}
