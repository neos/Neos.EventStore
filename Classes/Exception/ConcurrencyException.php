<?php
namespace Neos\EventStore\Exception;

/*
 * This file is part of the Neos.EventStore package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Cqrs\Exception;
use Neos\Cqrs\RuntimeException;

/**
 * ConcurrencyException
 */
class ConcurrencyException extends RuntimeException
{
    /**
     * @var array
     */
    protected $reasons = [];

    /**
     * @param string $message
     * @param array $reasons
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($message, array $reasons, int $code, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->reasons = $reasons;
    }

    /**
     * @return boolean
     */
    public function hasReasons(): bool
    {
        return $this->reasons !== [];
    }

    /**
     * @return array
     */
    public function getReasons(): array
    {
        return $this->reasons;
    }
}
