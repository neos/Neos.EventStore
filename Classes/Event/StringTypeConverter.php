<?php
namespace Ttree\EventStore\Event;

/*
 * This file is part of the Ttree.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Ttree\Cqrs\Event\EventInterface;
use Ttree\Cqrs\Event\EventTransport;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Property\PropertyMappingConfigurationInterface;
use TYPO3\Flow\Property\TypeConverter\AbstractTypeConverter;
use Zumba\JsonSerializer\JsonSerializer;

/**
 * StringTypeConverter
 */
class StringTypeConverter extends AbstractTypeConverter
{
    /**
     * @var array<string>
     */
    protected $sourceTypes = [EventTransport::class];

    /**
     * @var string
     */
    protected $targetType = 'string';

    /**
     * @param EventTransport $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param PropertyMappingConfigurationInterface $configuration
     * @return array
     * @api
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null)
    {
        $serializer = new JsonSerializer();
        return $serializer->serialize($source);
    }
}
