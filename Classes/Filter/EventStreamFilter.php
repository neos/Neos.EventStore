<?php
namespace Neos\EventStore\Filter;

/*
 * This file is part of the Neos.EventStore package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * EventStreamFilter
 */
class EventStreamFilter
{
    /**
     * @var int
     */
    public $minimumVersion = null;

    /**
     * @var int
     */
    public $maximumVersion = null;

    /**
     * @var \DateTimeImmutable
     */
    public $since = null;

    /**
     * @var \DateTimeImmutable
     */
    public $until = null;

    /**
     * @var string
     */
    public $streamName = null;

    /**
     * @var string
     */
    public $boundedContext = null;

    /**
     * @var string
     */
    public $aggregateName = null;

    /**
     * @var string
     */
    public $aggregateIdentifier = null;

    /**
     * @var string[]
     */
    public $eventTypes = null;

    /**
     * @return EventStreamFilter
     */
    public static function create()
    {
        return new EventStreamFilter();
    }

    /**
     * @return int
     */
    public function getMinimumVersion()
    {
        return $this->minimumVersion;
    }

    /**
     * @param int $minimumVersion
     * @return EventStreamFilter
     */
    public function setMinimumVersion(int $minimumVersion)
    {
        $this->minimumVersion = $minimumVersion;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaximumVersion()
    {
        return $this->maximumVersion;
    }

    /**
     * @param int $maximumVersion
     * @return EventStreamFilter
     */
    public function setMaximumVersion(int $maximumVersion)
    {
        $this->maximumVersion = $maximumVersion;
        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getSince()
    {
        return $this->since;
    }

    /**
     * @param \DateTimeImmutable $since
     * @return EventStreamFilter
     */
    public function setSince(\DateTimeImmutable $since)
    {
        $this->since = $since;
        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getUntil()
    {
        return $this->until;
    }

    /**
     * @param \DateTimeImmutable $until
     * @return EventStreamFilter
     */
    public function setUntil(\DateTimeImmutable $until)
    {
        $this->until = $until;
        return $this;
    }

    /**
     * @return string
     */
    public function getStreamName()
    {
        return $this->streamName;
    }

    /**
     * @param string $streamName
     * @return EventStreamFilter
     */
    public function setStreamName(string $streamName)
    {
        $this->streamName = $streamName;
        return $this;
    }

    /**
     * @return string
     */
    public function getBoundedContext()
    {
        return $this->boundedContext;
    }

    /**
     * @param string $boundedContext
     * @return EventStreamFilter
     */
    public function setBoundedContext(string $boundedContext)
    {
        $this->boundedContext = $boundedContext;
        return $this;
    }

    /**
     * @return string
     */
    public function getAggregateName()
    {
        return $this->aggregateName;
    }

    /**
     * @param string $aggregateName
     * @return EventStreamFilter
     */
    public function setAggregateName(string $aggregateName)
    {
        $this->aggregateName = $aggregateName;
        return $this;
    }

    /**
     * @return string
     */
    public function getAggregateIdentifier()
    {
        return $this->aggregateIdentifier;
    }

    /**
     * @param string $aggregateIdentifier
     * @return EventStreamFilter
     */
    public function setAggregateIdentifier(string $aggregateIdentifier)
    {
        $this->aggregateIdentifier = $aggregateIdentifier;
        return $this;
    }

    /**
     * @return \string[]
     */
    public function getEventTypes()
    {
        return $this->eventTypes;
    }

    /**
     * @param \string[] $eventTypes
     * @return EventStreamFilter
     */
    public function setEventTypes(array $eventTypes)
    {
        $this->eventTypes = $eventTypes;
        return $this;
    }
}
