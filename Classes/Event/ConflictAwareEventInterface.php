<?php
namespace Ttree\EventStore\Event;

/*
 * This file is part of the Ttree.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * ConflictAwareEventInterface
 */
interface ConflictAwareEventInterface
{
    /**
     * Return the list of conflicting events
     *
     * The return value is an associative array, the keys are the event type and
     * the value is the exception message explaining the reason of the conflict.
     *
     * If the methos return an emtpy array, the current event conflict with nothing.
     *
     * @return array
     */
    public static function conflictsWith(): array;
}
