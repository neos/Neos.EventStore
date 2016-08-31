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

use Neos\EventStore\Event\ConflictAwareEventInterface;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Object\ObjectManagerInterface;
use TYPO3\Flow\Reflection\ReflectionService;

/**
 * ConcurrencyConflictResolver
 *
 * @Flow\Scope("singleton")
 * @api
 */
class ConcurrencyConflictResolver implements ConcurrencyConflictResolverInterface
{
    /**
     * @var array
     */
    protected static $registry = [];

    /**
     * @var ObjectManagerInterface
     * @Flow\Inject
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $lastMessages = [];

    /**
     * Populate the registry
     */
    public function initializeObject()
    {
        foreach (self::populateRegistry($this->objectManager) as $eventType => $conflictsWith) {
            self::registerConflictWith($eventType, $conflictsWith);
        }
    }

    /**
     * Check if an event conflict with a previous event
     *
     * @param string $eventType
     * @param array $previousEventTypes
     * @return boolean
     */
    public function conflictWith(string $eventType, array $previousEventTypes): bool
    {
        // If type not registered assume the worst and say it conflicts
        if (!isset(self::$registry[$eventType])) {
            return true;
        }

        if (!self::$registry[$eventType]['hasConflictingEvents']) {
            return false;
        }

        $conflictingEvents = array_intersect($previousEventTypes, self::$registry[$eventType]['eventTypes']);

        $this->lastMessages = [];
        array_map(function ($eventType) {
            $this->lastMessages[$eventType] = self::$registry[$eventType]['messageMapping'];
        }, $conflictingEvents);

        return count($conflictingEvents) > 0;
    }

    /**
     * @return array
     */
    public function getLastMessages(): array
    {
        return $this->lastMessages;
    }

    /**
     * Register conflicting events
     *
     * The value of $conflictsWith is an associative array, the keys are the event type and
     * the value is the exception message.
     *
     * @param string $eventType
     * @param array $conflictsWith
     * @return void
     */
    public static function registerConflictWith(string $eventType, array $conflictsWith)
    {
        $eventTypes = array_keys($conflictsWith);
        self::$registry[$eventType] = [
            'eventTypes' => $eventTypes,
            'hasConflictingEvents' => count($eventTypes) > 0,
            'messageMapping' => isset ($conflictsWith[$eventType]) ? $conflictsWith[$eventType] : null
        ];
    }

    /**
     * @param ObjectManagerInterface $objectManager
     * @return array
     * @Flow\CompileStatic
     */
    protected static function populateRegistry(ObjectManagerInterface $objectManager)
    {
        $registry = [];
        /** @var ReflectionService $reflectionService */
        $reflectionService = $objectManager->get(ReflectionService::class);
        foreach ($reflectionService->getAllImplementationClassNamesForInterface(ConflictAwareEventInterface::class) as $eventType) {
            $registry[$eventType] = $eventType::conflictsWith();
        }
        return $registry;
    }
}
