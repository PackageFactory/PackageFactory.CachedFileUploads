<?php
declare(strict_types=1);

namespace PackageFactory\CachedFileUploads\TypeConverter;

/*
 * This file is part of the Neos.Flow package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Property\PropertyMappingConfigurationInterface;
use Neos\Flow\Property\TypeConverter\AbstractTypeConverter;
use Neos\Http\Factories\FlowUploadedFile;
use PackageFactory\CachedFileUploads\Domain\Model\UploadedFile;
use PackageFactory\CachedFileUploads\Domain\Repository\UploadedFileRepository;
use Psr\Http\Message\UploadedFileInterface;

/**
 * This converter transforms a session identifier into a real session object.
 *
 * Given a session ID this will return an instance of Neos\Flow\Session\Session.
 *
 * @api
 * @Flow\Scope("singleton")
 */
class UploadedFileConverter extends AbstractTypeConverter
{
    /**
     * @var array
     */
    protected $sourceTypes = [FlowUploadedFile::class];

    /**
     * @var string
     */
    protected $targetType = UploadedFile::class;

    /**
     * @var integer
     */
    protected $priority = 1;

    /**
     * @Flow\Inject
     * @var UploadedFileRepository
     */
    protected $uploadedFileRepository;

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
                return $this->uploadedFileRepository->findByIdentifier($originalResource['__identity']);
            } elseif (is_string($originalResource)) {
                return $this->uploadedFileRepository->findByIdentifier($originalResource);
            }
        }

        if (empty($source->getSize())) {
            return null;
        }

        $uploadedFile = UploadedFile::fromUploadedFile($source);
        $this->uploadedFileRepository->add($uploadedFile);

        return $uploadedFile;
    }
}
