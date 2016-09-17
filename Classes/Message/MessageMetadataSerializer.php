<?php
namespace Neos\EventStore\Message;

/*
 * This file is part of the Neos.EventStore package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Cqrs\Message\MessageMetadata;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Error;
use TYPO3\Flow\Property\Exception\TypeConverterException;
use TYPO3\Flow\Property\PropertyMappingConfigurationInterface;
use TYPO3\Flow\Property\TypeConverter\AbstractTypeConverter;
use TYPO3\Flow\Utility\TypeHandling;

/**
 * MessageMetadataSerializer
 */
class MessageMetadataSerializer extends AbstractTypeConverter
{
    /**
     * @var array
     */
    protected $sourceTypes = [MessageMetadata::class];

    /**
     * @var string
     */
    protected $targetType = 'array';

    /**
     * @param MessageMetadata $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param PropertyMappingConfigurationInterface $configuration
     * @return mixed|Error the target type, or an error object if a user-error occurred
     * @throws TypeConverterException thrown in case a developer error occurred
     * @api
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null)
    {
        return [
            'timestamp' => $convertedChildProperties['timestamp'],
            'metadata' => $convertedChildProperties['metadata'],
            '__type' => TypeHandling::getTypeForValue($source)
        ];
    }

    /**
     * @param MessageMetadata $source
     * @return array
     */
    public function getSourceChildPropertiesToBeConverted($source)
    {
        return ['timestamp' => $source->getTimestamp(), 'metadata' => $source->getProperties()];
    }


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
