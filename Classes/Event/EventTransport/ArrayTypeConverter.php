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

use Neos\Cqrs\Event\EventInterface;
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
