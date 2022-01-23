<?php
declare(strict_types=1);

namespace PackageFactory\CachedFileUploads\TypeConverter;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Property\PropertyMappingConfigurationInterface;
use Neos\Flow\Property\TypeConverter\AbstractTypeConverter;
use Neos\Http\Factories\FlowUploadedFile;
use PackageFactory\CachedFileUploads\Domain\CachedFileUpload;
use PackageFactory\CachedFileUploads\Domain\CachedFileUploadRepository;
use Psr\Http\Message\UploadedFileInterface;

/**
 * This converter transforms a session identifier into a real session object.
 *
 * Given a session ID this will return an instance of Neos\Flow\Session\Session.
 *
 * @api
 * @Flow\Scope("singleton")
 */
class CachedFileUploadConverter extends AbstractTypeConverter
{
    /**
     * @var CachedFileUploadRepository
     * @Flow\Inject
     */
    protected $cachedFileUploadRepository;

    /**
     * @var array
     */
    protected $sourceTypes = [FlowUploadedFile::class];

    /**
     * @var string
     */
    protected $targetType = CachedFileUpload::class;

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
        return ($source instanceof FlowUploadedFile);
    }

    /**
     * Convert an UploadedFileInterface to CachableUplaodedFile
     *
     * @param FlowUploadedFile $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param PropertyMappingConfigurationInterface|null $configuration
     * @return object the target type
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null)
    {
        if ($originalResource = $source->getOriginallySubmittedResource()) {
            if (is_array($originalResource) && array_key_exists('__identity', $originalResource)) {
                return $this->cachedFileUploadRepository->findByIdentifier($originalResource['__identity']);
            } elseif (is_string($originalResource)) {
                return $this->cachedFileUploadRepository->findByIdentifier($originalResource);
            }
        }

        if (empty($source->getSize())) {
            return null;
        }

        $uploadedFile = CachedFileUpload::fromUploadedFile($source);
        $this->cachedFileUploadRepository->add($uploadedFile);
        return $uploadedFile;
    }
}
