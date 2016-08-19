<?php
namespace Ttree\EventStore;

/*
 * This file is part of the Ttree.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * EventStreamData
 */
class EventStreamData
{
    /**
     * @var string
     */
    protected $aggregateIdentifier;

    /**
     * @var string
     */
    protected $aggregateName;

    /**
     * @var integer
     */
    protected $version;

    /**
     * @var array
     */
    protected $data;

    /**
     * @param string $aggregateIdentifier
     * @param string $aggregateName
     * @param array $data
     * @param integer $version
     */
    public function __construct(string $aggregateIdentifier, string $aggregateName, array $data, int $version)
    {
        $this->aggregateIdentifier = $aggregateIdentifier;
        $this->aggregateName = $aggregateName;
        $this->version = $version;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getAggregateIdentifier(): string
    {
        return $this->aggregateIdentifier;
    }

    /**
     * @return string
     */
    public function getAggregateName(): string
    {
        return $this->aggregateName;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @return \Generator
     */
    public function getData(): \Generator
    {
        foreach ($this->data as $event) {
            yield $event;
        }
    }
}
