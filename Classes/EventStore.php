<?php
namespace Ttree\EventStore;

/*
 * This file is part of the Ttree.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Ttree\Cqrs\Event\EventInterface;
use Ttree\EventStore\Exception\ConcurrencyException;
use Ttree\EventStore\Exception\EventStreamNotFoundException;
use Ttree\EventStore\Storage\EventStorageInterface;
use Ttree\EventStore\Storage\PreviousEventsInterface;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Property\PropertyMapper;
use TYPO3\Flow\Utility\Algorithms;

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
     * @var ConcurrencyConflictResolverInterface
     * @Flow\Inject
     */
    protected $conflictResolver;

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

        if ($newEvents === []) {
            return;
        }

        $commitIdentifier = Algorithms::generateUUID();
        $aggregateIdentifier = $stream->getAggregateIdentifier();
        $aggregateName = $stream->getAggregateName();
        $currentVersion = $stream->getVersion();

        $eventData = $this->convertToEventDataArray($newEvents);

        $nextVersion = $this->nextVersion($aggregateIdentifier, $currentVersion, $eventData);

        $this->storage->commit($commitIdentifier, $aggregateIdentifier, $aggregateName, $eventData, $nextVersion);

        $stream->markAllApplied($nextVersion);
    }

    /**
     * @param array $newEvents
     * @return array
     */
    protected function convertToEventDataArray(array $newEvents): array
    {
        return array_map(function (EventInterface $event) {
            return $this->propertyMapper->convert($event, 'array');
        }, $newEvents);
    }

    /**
     * @param string $aggregateIdentifier
     * @param integer $currentVersion
     * @param array $eventData
     * @return integer
     * @throws ConcurrencyException
     */
    protected function nextVersion(string $aggregateIdentifier, int $currentVersion, array $eventData): int
    {
        $eventCounter = count($eventData);
        $currentStoredVersion = $this->storage->getCurrentVersion($aggregateIdentifier);

        if ($currentVersion === $currentStoredVersion) {
            return $currentVersion + $eventCounter;
        }

        if (!$this->storage instanceof PreviousEventsInterface) {
            throw new ConcurrencyException(
                sprintf(
                    'Aggregate root versions mismatch, current stored version: %d, current version: %d',
                    $currentStoredVersion, $currentVersion
                ), [], 1472221044
            );
        }

        $previousEventData = $this->storage->getPreviousEvents($aggregateIdentifier, $currentVersion);
        $previousEventTypes = array_map(function (array $eventData) {
            return $eventData['type'];
        }, $previousEventData->getEvents());
        $messages = [];
        array_map(function (array $event) use ($previousEventTypes, &$messages) {
            $this->conflictResolver->conflictWith($event['type'], $previousEventTypes);
            $messages += $this->conflictResolver->getLastMessages();
        }, $eventData);

        if ($messages !== []) {
            $exception = new ConcurrencyException(
                sprintf(
                    'Aggregate root versions mismatch, current stored version: %d, current version: %d, unable to fix the conflict automatically',
                    $currentStoredVersion, $currentVersion
                ), $messages, 1472221024
            );
            throw $exception;
        }

        return $this->storage->getCurrentVersion($aggregateIdentifier) + $eventCounter;
    }

    /**
     * @param string $identifier
     * @return boolean
     */
    public function contains(string $identifier): bool
    {
        return $this->storage->contains($identifier);
    }
}
