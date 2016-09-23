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
 * AbstractStreamName
 */
abstract class AbstractStreamName
{
    const PATTERN = '%s::%s::%s';

    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $aggregateName
     * @param string $aggregateIdentifier
     */
    public function __construct(string $aggregateName, string $aggregateIdentifier)
    {
        $this->name = sprintf(self::PATTERN, $this->generatePackageKey(), $aggregateName, $aggregateIdentifier);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return string
     * @todo maybe move this one to a small utility class
     */
    protected function generatePackageKey()
    {
        $backtrace = debug_backtrace(false);
        $className = isset($backtrace[1]['class']) ? $backtrace[1]['class'] : null;
        $explodedClassName = explode('\\', $className);
        // FIXME: This is not really the package key:
        return isset($explodedClassName[1]) ? $explodedClassName[1] : null;
    }
}
