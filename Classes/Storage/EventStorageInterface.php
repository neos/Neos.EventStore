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
use Neos\EventStore\Exception\StorageConcurrencyException;

/**
 * EventStorageInterface
 */
interface EventStorageInterface
{
    /**
     * @param string $identifier
     * @return EventStreamData Aggregate Root events
     */
    public function load(string $identifier);

    /**
     * @param string $streamIdentifier
     * @param string $aggregateIdentifier
     * @param string $aggregateName
     * @param array $data
     * @param integer $version
     * @return void
     * @throws StorageConcurrencyException
     */
    public function commit(string $streamIdentifier, string $aggregateIdentifier, string $aggregateName, array $data, int $version);

    /**
     * @param string $identifier
     * @return boolean
     */
    public function contains(string $identifier): bool;

    /**
     * @param  string $identifier
     * @return integer Current Aggregate Root version
     */
    public function getCurrentVersion(string $identifier): int;
}
