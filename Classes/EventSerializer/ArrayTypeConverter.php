<?php
namespace Ttree\EventStore\EventSerializer;

/*
 * This file is part of the Ttree.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Ttree\Cqrs\Event\EventInterface;
use Ttree\Cqrs\Message\MessageMetadata;
use Ttree\EventStore\Exception\EventSerializerException;
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
     * @var array
     */
    protected static $schema = [
        'type',
        'aggregate_identifier',
        'name',
        'created_at',
        'payload'
    ];

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
        if (count(array_intersect_key(self::$schema, array_keys($source))) !== count(self::$schema)) {
            throw new EventSerializerException('No event class specified or invalid entry');
        }

        $payload = $source['payload'];
        foreach ($payload as $key => &$value) {
            if (!is_array($value) || !array_key_exists('type', $value)) {
                continue;
            }
            $value = $this->objectManager->get($value['type'], $value['value']);
        }

        /** @var EventInterface $event */
        $metaData = new MessageMetadata($source['name'], new \DateTime($source['created_at']));
        $event = $this->objectManager->get($source['type'], $source['payload'], $metaData);

        $event->setAggregateIdentifier($source['aggregate_identifier']);

        return $event;
    }
}
