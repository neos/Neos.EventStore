<?php
namespace Flowpack\EventStore\EventSerializer;

/*
 * This file is part of the Flowpack.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Flowpack\Cqrs\Event\EventInterface;
use Flowpack\Cqrs\Message\MessageMetadata;
use Flowpack\EventStore\Exception\EventSerializerException;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Error;
use TYPO3\Flow\Object\ObjectManagerInterface;
use TYPO3\Flow\Property\PropertyMappingConfigurationInterface;
use TYPO3\Flow\Property\TypeConverter\AbstractTypeConverter;

/**
 * ArrayTypeConverter
 */
class ArrayTypeConverter extends AbstractTypeConverter
{
    /**
     * @var array<string>
     */
    protected $sourceTypes = ['array'];

    /**
     * @var string
     */
    protected $targetType = EventInterface::class;

    /**
     * @var ObjectManagerInterface
     * @Flow\Inject
     */
    protected $objectManager;

    /**
     * @param array $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param PropertyMappingConfigurationInterface $configuration
     * @return mixed|Error the target type, or an error object if a user-error occurred
     * @throws EventSerializerException
     * @api
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null)
    {
        $schema = [
            'class',
            'aggregate_identifier',
            'name',
            'timestamp',
            'payload'
        ];

        if (count(array_intersect_key($schema, array_keys($source))) !== count($schema)) {
            throw new EventSerializerException('No event class specified or invalid entry');
        }

        $payload = $source['payload'];
        foreach ($payload as $key => &$value) {
            if (!is_array($value) || !array_key_exists('_php_class', $value)) {
                continue;
            }
            $value = $this->objectManager->get($value['_php_class'], $value['_value']);
        }

        /** @var EventInterface $event */
        $metaData = new MessageMetadata($source['name'], new \DateTime($source['timestamp']));
        $event = $this->objectManager->get($source['class'], $source['payload'], $metaData);

        $event->setAggregateIdentifier($source['aggregate_identifier']);

        return $event;
    }
}
