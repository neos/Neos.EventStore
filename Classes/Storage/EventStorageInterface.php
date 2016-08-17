<?php
namespace Flowpack\EventStore\Storage;

/*
 * This file is part of the Flowpack.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Flowpack\EventStore\EventStreamData;
use TYPO3\Flow\Annotations as Flow;

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
     * @param string $identifier
     * @param string $aggregateName
     * @param array $data
     * @param integer $version
     * @return void
     */
    public function commit(string $identifier, string $aggregateName, array $data, int $version);

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
