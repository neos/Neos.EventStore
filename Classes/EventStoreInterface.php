<?php
namespace Ttree\EventStore;

/*
 * This file is part of the Ttree.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * EventStoreInterface
 */
interface EventStoreInterface
{
    /**
     * @param  string $identifier
     * @return EventStream Can be empty stream
     */
    public function get(string $identifier): EventStream;

    /**
     * @param  string $identifier
     * @return boolean
     */
    public function contains(string $identifier): bool;

    /**
     * Persist new AR events
     * @param  EventStream $stream
     * @return integer commited version number
     * @throws \Exception
     */
    public function commit(EventStream $stream): int;
}
