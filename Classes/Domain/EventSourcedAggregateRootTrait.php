<?php
namespace Ttree\EventStore\Domain;

/*
 * This file is part of the Neos.EventStore package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Ttree\Cqrs\Domain\AggregateRootTrait;
use Ttree\Cqrs\Event\EventTransport;
use Ttree\Cqrs\RuntimeException;
use Ttree\EventStore\EventStream;

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

        $this->setAggregateIdentifier($stream->getAggregateIdentifier());

        /** @var EventTransport $eventTransport */
        foreach ($stream as $eventTransport) {
            $this->executeEvent($eventTransport->getEvent());
        }
    }
}
