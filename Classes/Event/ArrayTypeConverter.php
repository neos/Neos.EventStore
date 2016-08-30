<?php
namespace Ttree\EventStore\Event;

/*
 * This file is part of the Neos.EventStore package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Ttree\Cqrs\Domain\Timestamp;
use Ttree\Cqrs\Event\EventInterface;
use Ttree\EventStore\Exception\EventSerializerException;
use TYPO3\Flow\Property\PropertyMappingConfigurationInterface;
use TYPO3\Flow\Property\TypeConverter\AbstractTypeConverter;
use TYPO3\Flow\Reflection\ObjectAccess;

/**
 * ArrayTypeConverter
 */
class ArrayTypeConverter extends AbstractTypeConverter
{
    /**
     * @var array<string>
     */
    protected $sourceTypes = [EventInterface::class];

    /**
     * @var string
     */
    protected $targetType = 'array';

    /**
     * @param EventInterface $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param PropertyMappingConfigurationInterface $configuration
     * @return array
     * @throws EventSerializerException
     * @api
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null)
    {
        $data = ObjectAccess::getGettableProperties($source);
        foreach ($data as $propertyName => $propertyValue) {
            switch (true) {
                case $propertyValue instanceof \DateTime:
                    $propertyValue = $propertyValue->format(Timestamp::OUTPUT_FORMAT);
                    break;
                default:
                    if (!is_scalar($propertyValue)) {
                        throw new EventSerializerException('Event can only contains scalar type values or DataTime', 1472457099);
                    }
            }
            $data[$propertyName] = $propertyValue;
        }
        return $data;
    }
}
