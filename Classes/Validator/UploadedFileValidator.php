<?php
declare(strict_types=1);

namespace PackageFactory\CachedFileUploads\Validator;

use Neos\Flow\Validation\Validator\AbstractValidator;
use Psr\Http\Message\UploadedFileInterface;

/**
 * The given $value is valid if it is an \Psr\Http\Message\UploadedFileInterface; of the configured type
 * Note: a value of NULL or empty string ('') is considered valid
 */
class UploadedFileValidator extends AbstractValidator
{
    /**
     * @var mixed[]
     */
    protected $supportedOptions = array(
        'allowedExtensions' => array([], 'Array of allowed file extensions', 'array', false),
        'allowedMediaTypes' => array([], 'Array of allowed media types', 'array', false),
        'minSize' => array([], 'Min size in Bytes', 'int', false),
        'maxSize' => array([], 'Max size in Bytes', 'int', false)
    );

    /**
     * The given $value is valid if it is an \Psr\Http\Message\UploadedFileInterface of the configured resolution
     * Note: a value of NULL or empty string ('') is considered valid
     *
     * @param UploadedFileInterface $uploadedFile
     * @return void
     * @api
     */
    protected function isValid($uploadedFile)
    {
        if (!$uploadedFile instanceof UploadedFileInterface) {
            $this->addError('The given value was not an UploadedFile instance.', 1642936558);
            return;
        }
        if ($this->options['allowedExtensions'] && !in_array(pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION), $this->options['allowedExtensions'])) {
            $this->addError(
                'The file extension has to be one of "%s", "%s" is not allowed.',
                1642936508,
                [
                    implode(', ', $this->options['allowedExtensions']),
                    pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION)
                ]
            );
        }
        if ($this->options['allowedMediaTypes'] && !in_array($uploadedFile->getClientMediaType(), $this->options['allowedMediaTypes'])) {
            $this->addError(
                'The media type has to be one of "%s", "%s" is not allowed.',
                1642936548,
                [
                    implode(', ', $this->options['allowedMediaTypes']),
                    $uploadedFile->getClientMediaType()
                ]
            );
        }
        if ($this->options['minSize'] && ($uploadedFile->getSize() < $this->options['minSize'])) {
            $this->addError(
                'The media size i too small.',
                1642936541,
                [
                    implode(', ', $this->options['allowedMediaTypes']),
                    $uploadedFile->getClientMediaType()
                ]
            );
        }
        if ($this->options['maxSize'] && ($uploadedFile->getSize() > $this->options['maxSize'])) {
            $this->addError(
                'The media size i too big.',
                1642936537,
                [
                    implode(', ', $this->options['allowedMediaTypes']),
                    $uploadedFile->getClientMediaType()
                ]
            );
        }
    }
}
