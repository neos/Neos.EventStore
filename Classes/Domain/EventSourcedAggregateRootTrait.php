<?php
namespace Neos\EventStore\Domain;

/*
 * This file is part of the Neos.EventStore package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Cqrs\Domain\AggregateRootTrait;
use Neos\Cqrs\Event\EventTransport;
use Neos\Cqrs\RuntimeException;
use Neos\EventStore\EventStream;

/**
 * AggregateRootTrait
 */
trait EventSourcedAggregateRootTrait
{
    use AggregateRootTrait;

    /**
     * @param EventStream $stream
     * @throws RuntimeException
     */
    public function reconstituteFromEventStream(EventStream $stream)
    {
        if ($this->events) {
            throw new RuntimeException('AggregateRoot is already reconstituted from event stream.');
        }

        /** @var EventTransport $eventTransport */
        foreach ($stream as $eventTransport) {
            $this->apply($eventTransport->getEvent());
        }
    }
}
