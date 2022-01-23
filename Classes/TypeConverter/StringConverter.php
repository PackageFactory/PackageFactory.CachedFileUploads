<?php
declare(strict_types=1);

namespace PackageFactory\CachedFileUploads\TypeConverter;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Property\PropertyMappingConfigurationInterface;
use Neos\Flow\Property\TypeConverter\AbstractTypeConverter;
use PackageFactory\CachedFileUploads\Domain\CachedFileUpload;

/**
 * This converter transforms a session identifier into a real session object.
 *
 * Given a session ID this will return an instance of Neos\Flow\Session\Session.
 *
 * @api
 * @Flow\Scope("singleton")
 */
class StringConverter extends AbstractTypeConverter
{
    /**
     * @var array
     */
    protected $sourceTypes = [CachedFileUpload::class];

    /**
     * @var string
     */
    protected $targetType = 'string';

    /**
     * @var integer
     */
    protected $priority = 1;

    /**
     * This implementation always returns true for this method.
     *
     * @param mixed $source the source data
     * @param string $targetType the type to convert to.
     * @return boolean true if this TypeConverter can convert from $source to $targetType, false otherwise.
     * @api
     */
    public function canConvertFrom($source, $targetType)
    {
        return ($source instanceof CachedFileUpload);
    }

    /**
     * Convert an UploadedFileInterface to CachableUplaodedFile
     *
     * @param CachedFileUpload $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param PropertyMappingConfigurationInterface|null $configuration
     * @return object the target type
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null)
    {
        if (empty($source->getSize())) {
            return null;
        }
        return $source->getIdentifier();
    }
}
