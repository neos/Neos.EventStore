<?php
namespace Ttree\EventStore\Storage;

/*
 * This file is part of the Ttree.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Ttree\EventStore\EventStreamData;
use Ttree\EventStore\Exception\ConcurrencyException;
use Ttree\EventStore\Exception\StorageConcurrencyException;

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
