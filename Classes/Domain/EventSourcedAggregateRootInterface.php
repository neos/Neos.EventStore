<?php
namespace Flowpack\EventStore\Domain;

/*
 * This file is part of the Flowpack.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Flowpack\Cqrs\Domain\AggregateRootInterface;
use Flowpack\EventStore\EventStream;
use TYPO3\Flow\Annotations as Flow;

/**
 * AggregateRootInterface
 */
interface EventSourcedAggregateRootInterface extends AggregateRootInterface
{
    /**
     * @param EventStream $stream
     * @return void
     */
    public function reconstituteFromEventStream(EventStream $stream);
}
