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
use TYPO3\Flow\Property\Exception\InvalidTargetException;
use TYPO3\Flow\Property\PropertyMappingConfigurationInterface;
use TYPO3\Flow\Property\TypeConverter\ObjectConverter;
use TYPO3\Flow\Reflection\ObjectAccess;

/**
 * MessageMetadataConverter
 */
class MessageMetadataConverter extends ObjectConverter
{
    /**
     * @var array
     */
    protected $sourceTypes = ['array'];

    /**
     * @var string
     */
    protected $targetType = MessageMetadata::class;

    /**
     * @var integer
     */
    protected $priority = 100;

    /**
     * @param MessageMetadata $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param PropertyMappingConfigurationInterface $configuration
     * @return mixed|Error the target type, or an error object if a user-error occurred
     * @throws InvalidTargetException
     * @api
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null)
    {
        $object = $this->buildObject($convertedChildProperties, $targetType);
        foreach ($convertedChildProperties as $propertyName => $propertyValue) {
            $result = ObjectAccess::setProperty($object, $propertyName, $propertyValue, true);
            if ($result === false) {
                $exceptionMessage = sprintf(
                    'Property "%s" having a value of type "%s" could not be set in target object of type "%s".',
                    $propertyName,
                    (is_object($propertyValue) ? get_class($propertyValue) : gettype($propertyValue)),
                    $targetType
                );
                throw new InvalidTargetException($exceptionMessage, 1474142029);
            }
        }

        return $object;
    }

    /**
     * @param string $targetType
     * @param string $propertyName
     * @param PropertyMappingConfigurationInterface $configuration
     * @return string
     */
    public function getTypeOfChildProperty($targetType, $propertyName, PropertyMappingConfigurationInterface $configuration)
    {
        if ($propertyName === 'timestamp') {
            return 'DateTimeImmutable';
        }
        return 'array';
    }
}
