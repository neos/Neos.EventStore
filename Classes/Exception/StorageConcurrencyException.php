<?php
namespace Ttree\EventStore\Exception;

/*
 * This file is part of the Ttree.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Exception;
use Ttree\Cqrs\RuntimeException;
use TYPO3\Flow\Annotations as Flow;

/**
 * StorageConcurrencyException
 */
class StorageConcurrencyException extends ConcurrencyException
{

}
