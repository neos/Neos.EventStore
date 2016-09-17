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
     * @param string $streamName
     * @return EventStreamData Aggregate Root events
     */
    public function load(string $streamName);

    /**
     * @param string $streamName
     * @param array $data
     * @param integer $commitVersion
     * @return void
     * @throws StorageConcurrencyException
     */
    public function commit(string $streamName, array $data, int $commitVersion);

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
