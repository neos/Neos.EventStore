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
use Neos\EventStore\Filter\EventStreamFilter;
use Neos\EventStore\Storage\EventStorageInterface;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Log\SystemLoggerInterface;

/**
 * EventStore
 *
 * @Flow\Scope("singleton")
 */
class EventStore
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
     * @param EventStreamFilter $filter
     * @return EventStream Can be empty stream
     * @throws EventStreamNotFoundException
     */
    public function get(EventStreamFilter $filter): EventStream
    {
        /** @var EventStreamData $streamData */
        $streamData = $this->storage->load($filter);

        if (!$streamData || (!$streamData instanceof EventStreamData)) {
            throw new EventStreamNotFoundException();
        }

        return new EventStream(
            $streamData->getData(),
            $streamData->getVersion()
        );
    }

    /**
     * @param string $streamName
     * @param EventStream $stream
     * @return int commited version number
     * @param \Closure $callbackWithinTransaction execute the callback within a transaction in the store
     * @throws ConcurrencyException
     */
    public function commit(string $streamName, EventStream $stream, \Closure $callbackWithinTransaction = null) :int
    {
        $newEvents = $stream->getNewEvents();

        if ($newEvents === []) {
            return $this->storage->getCurrentVersion($streamName);
        }

        $currentVersion = $stream->getVersion();
        $expectedVersion = $currentVersion + count($newEvents);
        $this->storage->commit($streamName, $newEvents, $expectedVersion, $callbackWithinTransaction);
        $stream->markAllApplied($expectedVersion);

        return $expectedVersion;
    }
}
