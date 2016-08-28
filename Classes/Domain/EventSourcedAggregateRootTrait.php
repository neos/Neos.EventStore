<?php
namespace Ttree\EventStore\Domain;

/*
 * This file is part of the Ttree.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Ttree\Cqrs\Domain\AggregateRootTrait;
use Ttree\Cqrs\Event\EventInterface;
use Ttree\Cqrs\Event\EventTransport;
use Ttree\Cqrs\RuntimeException;
use Ttree\EventStore\EventStream;
use TYPO3\Flow\Annotations as Flow;

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
