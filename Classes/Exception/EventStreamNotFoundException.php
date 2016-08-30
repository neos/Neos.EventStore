<?php
namespace Ttree\EventStore\Exception;

/*
 * This file is part of the Neos.EventStore package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Ttree\Cqrs\RuntimeException;
use TYPO3\Flow\Annotations as Flow;

/**
 * EventStreamNotFoundException
 */
class EventStreamNotFoundException extends RuntimeException
{
}
