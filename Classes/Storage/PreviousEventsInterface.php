<?php
namespace Ttree\EventStore\Storage;

/*
 * This file is part of the Neos.EventStore package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
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
