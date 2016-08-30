<?php
namespace Ttree\EventStore;

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
 * ConcurrencyConflictResolverInterface
 */
interface ConcurrencyConflictResolverInterface
{
    /**
     * Check if an event conflict with a previous event
     *
     * @param string $eventType
     * @param array $previousEventTypes
     * @return boolean
     */
    public function conflictWith(string $eventType, array $previousEventTypes): bool;

    /**
     * Return the error message from the last call of conflictWith method
     * @return array
     */
    public function getLastMessages(): array;

    /**
     * Register conflicting events
     *
     * The value of $conflictsWith is an associative array, the keys are the event type and
     * the value is the exception message.
     *
     * @param string $eventType
     * @param array $conflictsWith
     * @return void
     */
    public static function registerConflictWith(string $eventType, array $conflictsWith);
}
