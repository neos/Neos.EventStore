<?php
namespace Ttree\EventStore\EventSerializer;

/*
 * This file is part of the Ttree.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Ttree\Cqrs\Domain\Timestamp;
use Ttree\Cqrs\Event\EventInterface;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Object\ObjectManagerInterface;
use TYPO3\Flow\Property\TypeConverter\AbstractTypeConverter;

/**
 * EventTypeConverter
 */
class EventTypeConverter extends AbstractTypeConverter
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
     * @var ObjectManagerInterface
     * @Flow\Inject
     */
    protected $objectManager;

    /**
     * @param EventInterface $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param \TYPO3\Flow\Property\PropertyMappingConfigurationInterface $configuration
     * @return mixed|\TYPO3\Flow\Error\Error the target type, or an error object if a user-error occurred
     * @throws \TYPO3\Flow\Property\Exception\TypeConverterException thrown in case a developer error occurred
     * @api
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], \TYPO3\Flow\Property\PropertyMappingConfigurationInterface $configuration = null)
    {
        $payload = $source->getPayload();

        foreach ($payload as $key => &$value) {
            if ($value instanceof \DateTime) {
                $value = [
                    'type' => \DateTime::class,
                    'value' => $value->format(Timestamp::OUTPUT_FORMAT)
                ];
            }
        }

        $data = [
            'type' => get_class($source),
            'aggregate_identifier' => $source->getAggregateIdentifier(),
            'name' => $source->getName(),
            'created_at' => $source->getTimestamp()->format(Timestamp::OUTPUT_FORMAT),
            'payload' => $payload
        ];

        return $data;
    }
}
