<?php
namespace Neos\EventStore\Property\TypeConverter;

/*
 * This file is part of the Neos.EventStore package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Error;
use TYPO3\Flow\Property\Exception\TypeConverterException;
use TYPO3\Flow\Property\PropertyMappingConfigurationInterface;
use TYPO3\Flow\Property\TypeConverter\DateTimeConverter;

/**
 * DateTimeImmutableConverter
 */
class DateTimeImmutableConverter extends DateTimeConverter
{
    /**
     * @var string
     */
    protected $targetType = 'DateTimeImmutable';

    /**
     * @var integer
     */
    protected $priority = 100;

    /**
     * @param \DateTimeImmutable $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param PropertyMappingConfigurationInterface $configuration
     * @return mixed|Error the target type, or an error object if a user-error occurred
     * @throws TypeConverterException thrown in case a developer error occurred
     * @api
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null)
    {
        return \DateTimeImmutable::createFromMutable(
            parent::convertFrom($source, 'DateTime', $convertedChildProperties, $configuration)
        );
    }
}
