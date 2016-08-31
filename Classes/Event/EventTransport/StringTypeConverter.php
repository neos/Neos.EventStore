<?php
namespace Neos\EventStore\Event\EventTransport;

/*
 * This file is part of the Neos.EventStore package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Cqrs\Event\EventTransport;
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
