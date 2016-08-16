<?php
namespace Flowpack\EventStore\Domain;

/*
 * This file is part of the Flowpack.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Flowpack\Cqrs\Domain\AggregateRootTrait;
use Flowpack\Cqrs\Event\EventInterface;
use Flowpack\EventStore\EventStream;
use Flowpack\Cqrs\RuntimeException;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Utility\Arrays;

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

        /** @var EventInterface $event */
        foreach ($stream as $event) {
            $this->executeEvent($event);
        }
    }
}
