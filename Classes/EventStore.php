<?php
namespace Flowpack\EventStore;

/*
 * This file is part of the Flowpack.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Flowpack\Cqrs\Event\EventInterface;
use Flowpack\EventStore\Exception\ConcurrencyException;
use Flowpack\EventStore\Exception\EventStreamNotFoundException;
use Flowpack\EventStore\Storage\EventStorageInterface;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Property\PropertyMapper;

/**
 * EventStore
 */
class EventStore implements EventStoreInterface
{
    /**
     * @var EventStorageInterface
     * @Flow\Inject
     */
    protected $storage;

    /**
     * @var PropertyMapper
     * @Flow\Inject
     */
    protected $propertyMapper;

    /**
     * Get events for AR
     * @param  string $identifier
     * @return EventStream Can be empty stream
     * @throws EventStreamNotFoundException
     */
    public function get(string $identifier): EventStream
    {
        /** @var EventStreamData $streamData */
        $streamData = $this->storage->load($identifier);

        if (!$streamData || (!$streamData instanceof EventStreamData)) {
            throw new EventStreamNotFoundException();
        }

        $events = [];

        foreach ($streamData->getData() as $eventData) {
            $events[] = $this->propertyMapper->convert($eventData, EventInterface::class);
        }

        return new EventStream(
            $streamData->getAggregateIdentifier(),
            $streamData->getAggregateName(),
            $events,
            $streamData->getVersion()
        );
    }

    /**
     * Persist new AR events
     * @param  EventStream $stream
     * @throws ConcurrencyException
     */
    public function commit(EventStream $stream)
    {
        $newEvents = $stream->getNewEvents();

        $newEventsQuantity = count($newEvents);

        if (!$newEventsQuantity) {
            return;
        }

        $aggregateIdentifier = $stream->getAggregateIdentifier();
        $aggregateName = $stream->getAggregateName();
        $currentVersion = $stream->getVersion();
        $nextVersion = $currentVersion + $newEventsQuantity;

        $eventData = [];

        /** @var EventInterface $event */
        foreach ($newEvents as $event) {
            $eventData[] = $this->propertyMapper->convert($event, 'array');
        }

        $currentStoredVersion = $this->storage->getCurrentVersion($aggregateIdentifier);

        if ($currentVersion !== $currentStoredVersion) {
            throw new ConcurrencyException(
                sprintf('Aggregate root versions mismatch, current stored version: %d, current version: %d', $currentStoredVersion, $currentVersion)
            );
        }

        $this->storage->commit($aggregateIdentifier, $aggregateName, $eventData, $nextVersion);

        $stream->markAllApplied($nextVersion);

        return;
    }
}
