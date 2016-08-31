<?php
namespace Neos\EventStore\Event;

/*
 * This file is part of the Neos.EventStore package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
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
