<?php
namespace Neos\EventStore\Stream;

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
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Utility\TypeHandling;

/**
 * StreamName
 */
class AggregateStreamName extends AbstractStreamName
{
    /**
     * @param AggregateRootInterface $aggregate
     * @return string
     */
    public static function generate(AggregateRootInterface $aggregate)
    {
        $type = TypeHandling::getTypeForValue($aggregate);
        $aggregateName = substr($type, strrpos($type, '\\') + 1);
        $name = new StreamName($aggregateName, $aggregate->getAggregateIdentifier());
        return (string)$name;
    }
}
