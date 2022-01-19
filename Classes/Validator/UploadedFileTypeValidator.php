<?php
declare(strict_types=1);

namespace PackageFactory\CachedFileUploads\Validator;

/*
 * This file is part of the Neos.Fusion.Form package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Validation\Validator\AbstractValidator;
use Psr\Http\Message\UploadedFileInterface;

/**
 * The given $value is valid if it is an \Psr\Http\Message\UploadedFileInterface; of the configured type
 * Note: a value of NULL or empty string ('') is considered valid
 */
class UploadedFileTypeValidator extends AbstractValidator
{
    /**
     * @var mixed[]
     */
    protected $supportedOptions = array(
        'allowedExtensions' => array([], 'Array of allowed file extensions', 'array', false),
        'allowedMediaTypes' => array([], 'Array of allowed media types', 'array', false)
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
            $this->addError('The given value was not a UploadedFile instance.', 1616425674);
            return;
        }
        if ($this->options['allowedExtensions'] && !in_array(pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION), $this->options['allowedExtensions'])) {
            $this->addError(
                'The file extension has to be one of "%s", "%s" is not allowed.',
                1616425683,
                [
                    implode(', ', $this->options['allowedExtensions']),
                    pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION)
                ]
            );
        }
        if ($this->options['allowedMediaTypes'] && !in_array($uploadedFile->getClientMediaType(), $this->options['allowedMediaTypes'])) {
            $this->addError(
                'The media type has to be one of "%s", "%s" is not allowed.',
                1616425912,
                [
                    implode(', ', $this->options['allowedMediaTypes']),
                    $uploadedFile->getClientMediaType()
                ]
            );
        }
    }
}
