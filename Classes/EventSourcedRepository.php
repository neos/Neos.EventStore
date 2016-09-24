<?php
namespace Neos\EventStore;

/*
 * This file is part of the Neos.EventStore package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Cqrs\Domain\AggregateRootInterface;
use Neos\Cqrs\Domain\Exception\AggregateRootNotFoundException;
use Neos\Cqrs\Domain\RepositoryInterface;
use Neos\Cqrs\Event\EventHandlingService;
use Neos\EventStore\Domain\EventSourcedAggregateRootInterface;
use Neos\EventStore\Exception\EventStreamNotFoundException;
use Neos\EventStore\Filter\EventStreamFilter;
use TYPO3\Flow\Annotations as Flow;

/**
 * EventSourcedRepository
 */
abstract class EventSourcedRepository implements RepositoryInterface
{
    /**
     * @var EventStore
     * @Flow\Inject
     */
    protected $eventStore;

    /**
     * @var EventHandlingService
     * @Flow\Inject
     */
    protected $eventHandlingService;

    /**
     * @var string
     */
    protected $aggregateClassName;

    /**
     * @var string
     */
    protected $streamNamePrefix;

    /**
     * Initializes a new Repository.
     */
    public function __construct()
    {
        $this->aggregateClassName = preg_replace(['/Repository$/'], [''], get_class($this));
    }

    /**
     * @param string $identifier
     * @return AggregateRootInterface
     * @throws AggregateRootNotFoundException
     */
    public function findByIdentifier($identifier)
    {
        try {
            /** @var EventStream $eventStream */
            $filter = EventStreamFilter::create()
                ->setStreamName($this->getStreamName($identifier));
            $eventStream = $this->eventStore->get($filter);
        } catch (EventStreamNotFoundException $e) {
            return null;
        }

        if (!class_exists($this->aggregateClassName)) {
            throw new AggregateRootNotFoundException(sprintf("Could not reconstitute the aggregate root %s because its class '%s' does not exist.", $identifier, $this->aggregateClassName), 1474454928115);
        }

        $aggregateRoot = unserialize('O:' . strlen($this->aggregateClassName) . ':"' . $this->aggregateClassName . '":0:{};');

        if (!$aggregateRoot instanceof EventSourcedAggregateRootInterface) {
            throw new AggregateRootNotFoundException(sprintf("Could not reconstitute the aggregate root '%s' with id '%s' because it does not implement the EventSourcedAggregateRootInterface.", $this->aggregateClassName, $identifier, $this->aggregateClassName), 1474464335530);
        }
        $aggregateRoot->reconstituteFromEventStream($eventStream);
        return $aggregateRoot;
    }

    /**
     * @param  AggregateRootInterface $aggregate
     * @return void
     */
    public function save(AggregateRootInterface $aggregate)
    {
        try {
            $filter = EventStreamFilter::create()
                ->setStreamName($this->getStreamName($aggregate->getAggregateIdentifier()));
            $stream = $this->eventStore->get($filter);
        } catch (EventStreamNotFoundException $e) {
            $stream = new EventStream();
        }

        $uncommittedEvents = $aggregate->pullUncommittedEvents();
        $stream->addEvents(...$uncommittedEvents);

        $this->eventHandlingService->publish($this->getStreamName($aggregate->getAggregateIdentifier()), $stream);
    }

    /**
     * @param string $identifier
     * @return string
     */
    protected function getStreamName($identifier)
    {
        if ($this->streamNamePrefix === null) {
            list($vendor, $package) = explode('\\', $this->aggregateClassName);
            $aggregateName = substr($this->aggregateClassName, strrpos($this->aggregateClassName, '\\') + 1);
            $this->streamNamePrefix = $vendor . ':' . $package . ':' . $aggregateName;
        }
        return $this->streamNamePrefix . ':' . $identifier;
    }
}
