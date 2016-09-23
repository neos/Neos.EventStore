<?php
namespace Neos\EventStore\Storage;

/*
 * This file is part of the Neos.EventStore package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\EventStore\EventStreamData;
use Neos\EventStore\Exception\ConcurrencyException;
use Neos\EventStore\Exception\StorageConcurrencyException;
use Neos\EventStore\Filter\EventStreamFilter;

/**
 * EventStorageInterface
 */
interface EventStorageInterface
{
    /**
     * @param EventStreamFilter $filter
     * @return EventStreamData Aggregate Root events
     */
    public function load(EventStreamFilter $filter);

    /**
     * @param string $streamName
     * @param array $data
     * @param integer $expectedVersion
     * @param \Closure $callback
     * @return void
     * @throws ConcurrencyException
     */
    public function commit(string $streamName, array $data, int $expectedVersion, \Closure $callback = null);

    /**
     * @param string $streamName
     * @return boolean
     */
    public function contains(string $streamName): bool;

    /**
     * @param  string $streamName
     * @return integer Current Aggregate Root version
     */
    public function getCurrentVersion(string $streamName): int;
}
