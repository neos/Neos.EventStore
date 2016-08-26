<?php
namespace Ttree\EventStore\Storage;

/*
 * This file is part of the Ttree.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Ttree\EventStore\EventStream;

/**
 * PreviousEventsInterface
 */
interface PreviousEventsInterface
{
    /**
     * @param string $identifier
     * @param integer $untilVersion
     * @return EventStream
     */
    public function getPreviousEvents(string $identifier, int $untilVersion): EventStream;
}
