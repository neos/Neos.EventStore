<?php
namespace Ttree\EventStore\Event;

/*
 * This file is part of the Ttree.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Ttree\Cqrs\Domain\Timestamp;
use Ttree\Cqrs\Event\EventInterface;
use Ttree\Cqrs\Event\EventTransport;
use Ttree\EventStore\Exception\EventSerializerException;
use TYPO3\Flow\Annotations as Flow;
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
