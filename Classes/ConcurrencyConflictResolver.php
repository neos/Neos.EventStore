<?php
namespace Ttree\EventStore;

/*
 * This file is part of the Ttree.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Ttree\Cqrs\Event\ConflictAwareEventInterface;
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
        return count($conflictingEvents) > 0;
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
            'messageMapping' => $conflictsWith
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
