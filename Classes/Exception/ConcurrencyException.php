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
