<?php
namespace Neos\EventStore;

/*
 * This file is part of the Neos.EventStore package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\EventStore\Exception\ConcurrencyException;
use Neos\EventStore\Exception\EventStreamNotFoundException;
use Neos\EventStore\Storage\EventStorageInterface;
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
     * @return integer commited version number
     * @throws ConcurrencyException
     */
    public function commit(EventStream $stream) :int
    {
        $newEvents = $stream->getNewEvents();

        $streamIdentifier = $stream->getIdentifier();
        $aggregateIdentifier = $stream->getAggregateIdentifier();
        if ($newEvents === []) {
            return $this->storage->getCurrentVersion($aggregateIdentifier);
        }

        $aggregateName = $stream->getAggregateName();
        $currentVersion = $stream->getVersion();

        $version = $this->nextVersion($aggregateIdentifier, $currentVersion, $newEvents);
        $this->storage->commit($streamIdentifier, $aggregateIdentifier, $aggregateName, $newEvents, $version);
        $stream->markAllApplied($version);

        return $version;
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

        if ($currentVersion !== $currentStoredVersion) {
            throw new ConcurrencyException(
                vsprintf(
                    'Aggregate root versions mismatch, current stored version: %d, current version: %d',
                    [$currentStoredVersion, $currentVersion]
                ), [], 1472221044
            );
        }

        return $currentVersion + $eventCounter;
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
