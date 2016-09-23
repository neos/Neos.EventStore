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

use TYPO3\Flow\Annotations as Flow;

/**
 * StreamName
 */
class StreamName extends AbstractStreamName
{
    /**
     * @param string $aggregateName
     * @param string $aggregateIdentifier
     * @return string
     */
    public static function generate(string $aggregateName, string $aggregateIdentifier)
    {
        $name = new StreamName($aggregateName, $aggregateIdentifier);
        return (string)$name;
    }
}
