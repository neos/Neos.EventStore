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
