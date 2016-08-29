<?php
namespace Ttree\EventStore\Event\EventTransport;

/*
 * This file is part of the Ttree.Cqrs package.
 *
 * (c) Hand crafted with love in each details by medialib.tv
 */

use Ttree\Cqrs\Event\EventInterface;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Property\PropertyMappingConfigurationInterface;

/**
 * ArrayTypeConverter
 */
class ArrayTypeConverter extends StringTypeConverter
{
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
     * @api
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null)
    {
        $data = parent::convertFrom($source, $targetType, $convertedChildProperties, $configuration);
        return json_decode($data, true);
    }
}
