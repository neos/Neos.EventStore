<?php
namespace Ttree\EventStore\Event\EventTransport;

/*
 * This file is part of the Ttree.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Ttree\Cqrs\Event\EventTransport;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Error;
use TYPO3\Flow\Object\ObjectManagerInterface;
use TYPO3\Flow\Property\Exception\TypeConverterException;
use TYPO3\Flow\Property\PropertyMappingConfigurationInterface;
use TYPO3\Flow\Property\TypeConverter\AbstractTypeConverter;
use Zumba\JsonSerializer\JsonSerializer;

/**
 * EventTransportTypeConverter
 */
class EventTransportTypeConverter extends AbstractTypeConverter
{
    /**
     * @var array<string>
     */
    protected $sourceTypes = ['array', 'string'];

    /**
     * @var string
     */
    protected $targetType = EventTransport::class;

    /**
     * @param array|string $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param PropertyMappingConfigurationInterface $configuration
     * @return mixed|Error the target type, or an error object if a user-error occurred
     * @throws TypeConverterException
     * @api
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null)
    {
        $serializer = new JsonSerializer();
        if (is_string($source)) {
            $source = json_decode($source, true);
            if ($source === null) {
                throw new TypeConverterException('Unable to decode JSON string', 1472297993);
            }
        }
        return $serializer->unserialize(json_encode($source));
    }
}
