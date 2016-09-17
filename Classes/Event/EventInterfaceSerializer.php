<?php
namespace Neos\EventStore\Event;

/*
 * This file is part of the Neos.EventStore package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Cqrs\Event\EventInterface;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Property\PropertyMappingConfigurationInterface;
use TYPO3\Flow\Property\TypeConverter\ArrayFromObjectConverter;

/**
 * EventInterfaceSerializer
 */
class EventInterfaceSerializer extends ArrayFromObjectConverter
{
    /**
     * @var array
     */
    protected $sourceTypes = [EventInterface::class];

    /**
     * @var string
     */
    protected $targetType = 'array';

    /**
     * @var integer
     */
    protected $priority = 100;

    /**
     * @param string $targetType
     * @param string $propertyName
     * @param PropertyMappingConfigurationInterface $configuration
     * @return string
     */
    public function getTypeOfChildProperty($targetType, $propertyName, PropertyMappingConfigurationInterface $configuration)
    {
        return 'array';
    }
}
