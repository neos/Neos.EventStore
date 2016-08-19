<?php
namespace Ttree\EventStore\Domain;

/*
 * This file is part of the Ttree.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Ttree\Cqrs\Domain\AggregateRootInterface;
use Ttree\EventStore\EventStream;
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
