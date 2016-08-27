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
use TYPO3\Flow\Log\SystemLoggerInterface;

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
     * @var SystemLoggerInterface
     * @Flow\Inject
     */
    protected $logger;

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

        return new EventStream(
            $streamData->getAggregateIdentifier(),
            $streamData->getAggregateName(),
            $streamData->getData(),
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

        $streamIdentifier = $stream->getIdentifier();
        $aggregateIdentifier = $stream->getAggregateIdentifier();
        $aggregateName = $stream->getAggregateName();
        $currentVersion = $stream->getVersion();

        $nextVersion = $this->nextVersion($aggregateIdentifier, $currentVersion, $newEvents);

        $this->storage->commit($streamIdentifier, $aggregateIdentifier, $aggregateName, $newEvents, $nextVersion);

        $stream->markAllApplied($nextVersion);
    }

    /**
     * Generate the next version for the current commit
     *
     * By default the next version is the current version + the number of new events,
     * if we detect conflict an exception is throwned the clear message to help the
     * user in the resolution of the message.
     *
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

        return $this->conflictsResolution($aggregateIdentifier, $currentVersion, $currentStoredVersion, $eventData);
    }

    /**
     * @param string $aggregateIdentifier
     * @param integer $currentVersion
     * @param integer $currentStoredVersion
     * @param array $eventData
     * @return integer
     * @throws ConcurrencyException
     */
    protected function conflictsResolution(string $aggregateIdentifier, int $currentVersion, int $currentStoredVersion, array $eventData): int
    {
        if (!$this->storage instanceof PreviousEventsInterface) {
            throw new ConcurrencyException(
                sprintf(
                    'Aggregate root versions mismatch, current stored version: %d, current version: %d',
                    $currentStoredVersion, $currentVersion
                ), [], 1472221044
            );
        }
        $eventCounter = count($eventData);

        $previousEventData = $this->storage->getPreviousEvents($aggregateIdentifier, $currentVersion);
        $previousEventTypes = array_map(function (EventInterface $event) {
            return $event->getName();
        }, $previousEventData->getEvents());
        $messages = [];
        array_map(function (EventInterface $event) use ($previousEventTypes, &$messages) {
            $this->conflictResolver->conflictWith($event->getName(), $previousEventTypes);
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
        $this->logger->log('Aggregate root versions mismatch, but solved automatically for you', LOG_NOTICE);
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
