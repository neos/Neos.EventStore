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
use Neos\Cqrs\Event\EventBus;
use Neos\Cqrs\Event\EventTransport;
use Neos\EventStore\Domain\EventSourcedAggregateRootInterface;
use Neos\EventStore\Event\Metadata;
use Neos\EventStore\Exception\EventStreamNotFoundException;
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
     * @var EventBus
     * @Flow\Inject
     */
    protected $eventBus;

    /**
     * @var string
     */
    protected $aggregateClassName;

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
    public function findByIdentifier($identifier): AggregateRootInterface
    {
        try {
            /** @var EventStream $eventStream */
            $eventStream = $this->eventStore->get($this->generateStreamName($identifier));
        } catch (EventStreamNotFoundException $e) {
            throw new AggregateRootNotFoundException(sprintf(
                "AggregateRoot with id '%s' not found", $identifier
            ), 1471077948);
        }

        /** @var EventSourcedAggregateRootInterface $aggregateRoot */
        $aggregateRoot = unserialize('O:' . strlen($this->aggregateClassName) . ':"' . $this->aggregateClassName . '":0:{};');
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
            $stream = $this->eventStore->get($this->generateStreamName($aggregate->getAggregateIdentifier()));
        } catch (EventStreamNotFoundException $e) {
            $stream = new EventStream();
        }

        $uncommittedEvents = $aggregate->pullUncommittedEvents();
        $stream->addEvents(...$uncommittedEvents);

        $this->eventStore->commit($this->generateStreamName($aggregate->getAggregateIdentifier()), $stream, function ($version) use ($uncommittedEvents) {
            /** @var EventTransport $eventTransport */
            foreach ($uncommittedEvents as $eventTransport) {
                // @todo metadata enrichment must be done in external service, with some middleware support
                $versionedMetadata = $eventTransport->getMetadata()->withProperty(Metadata::VERSION, $version);
                $this->eventBus->handle($eventTransport->withMetadata($versionedMetadata));
            }
        });
    }

    /**
     * @param string $identifier
     * @return boolean
     */
    public function contains($identifier): bool
    {
        return $this->eventStore->contains($this->generateStreamName($identifier));
    }

    /**
     * @param string $identifier
     * @return string
     * @todo find a more flexible way to generate stream name, need to be discussed
     */
    protected function generateStreamName(string $identifier)
    {
        $streamName = $this->aggregateClassName . '::' . $identifier;
        return $streamName;
    }
}
