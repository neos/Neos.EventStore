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
use Neos\Cqrs\Event\EventBusInterface;
use Neos\Cqrs\Event\EventTransport;
use Neos\EventStore\Domain\EventSourcedAggregateRootInterface;
use Neos\EventStore\Event\Metadata;
use Neos\EventStore\Exception\EventStreamNotFoundException;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Utility\TypeHandling;

/**
 * EventSourcedRepository
 */
abstract class EventSourcedRepository implements RepositoryInterface
{
    /**
     * @var EventStoreInterface
     * @Flow\Inject
     */
    protected $eventStore;

    /**
     * @var EventBusInterface
     * @Flow\Inject
     */
    protected $eventBus;

    /**
     * @param string $identifier
     * @return AggregateRootInterface
     * @throws AggregateRootNotFoundException
     */
    public function findByIdentifier($identifier): AggregateRootInterface
    {
        try {
            /** @var EventStream $eventStream */
            $eventStream = $this->eventStore->get($identifier);
        } catch (EventStreamNotFoundException $e) {
            throw new AggregateRootNotFoundException(sprintf(
                "AggregateRoot with id '%s' not found", $identifier
            ), 1471077948);
        }

        /** @var EventSourcedAggregateRootInterface $aggregateRoot */
        $aggregateRoot = unserialize('O:' . strlen($eventStream->getAggregateName()) . ':"' . $eventStream->getAggregateName() . '":0:{};');
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
            $stream = $this->eventStore
                ->get($aggregate->getAggregateIdentifier());
        } catch (EventStreamNotFoundException $e) {
            $stream = new EventStream(
                $aggregate->getAggregateIdentifier(),
                TypeHandling::getTypeForValue($aggregate),
                []
            );
        } finally {
            $uncommitedEvents = $aggregate->pullUncommittedEvents();
            $stream->addEvents(...$uncommitedEvents);
        }

        $version = $this->eventStore->commit($stream);

        /** @var EventTransport $eventTransport */
        foreach ($uncommitedEvents as $eventTransport) {
            // @todo metadata enrichment must be done in external service, with some middleware support
            $eventTransport->getMetaData()->add(Metadata::VERSION, $version);

            $this->eventBus->handle($eventTransport);
        }
    }

    /**
     * @param string $identifier
     * @return boolean
     */
    public function contains($identifier): bool
    {
        return $this->eventStore->contains($identifier);
    }
}
