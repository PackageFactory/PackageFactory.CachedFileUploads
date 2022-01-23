<?php
declare(strict_types=1);

namespace PackageFactory\CachedFileUploads\Domain;

use Neos\Flow\Annotations as Flow;
use Neos\Cache\Frontend\StringFrontend;
use Neos\Flow\Utility\Algorithms;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @Flow\Entity
 */
class CachedFileUpload implements UploadedFileInterface, \JsonSerializable
{
    /**
     * @Flow\Inject
     * @var StreamFactoryInterface
     */
    protected $streamFactory;

    /**
     * @Flow\Inject
     * @var StringFrontend
     */
    protected $contentCache;

    /**
     * @var string
     * @Flow\Identity
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var int
     */
    protected $error;

    /**
     * @var string
     */
    protected $clientFilename;

    /**
     * @var string
     */
    protected $clientMediaType;

    /**
     * UploadedFile constructor.
     *
     * @param string $content
     * @param int $size
     * @param int $error
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     */
    protected function __construct(string $content, int $size, int $error, string $clientFilename = null, string $clientMediaType = null)
    {
        $this->identifier = Algorithms::generateUUID();
        $this->content = $content;
        $this->size = $size;
        $this->error = $error;
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;
    }

    /**
     * on initialize the content is stored in the cache aswell
     * because the serialisation will skip the content part
     *
     * @return void
     * @throws \Neos\Cache\Exception
     */
    public function initializeObject()
    {
        if ($this->content) {
            $this->contentCache->set($this->identifier, $this->content);
        }
    }

    /**
     * @param UploadedFileInterface $uploadedFile
     * @return CachedFileUpload
     */
    public static function fromUploadedFile(UploadedFileInterface $uploadedFile): self
    {
        return new static(
            $uploadedFile->getStream()->getContents(),
            $uploadedFile->getSize(),
            $uploadedFile->getError(),
            $uploadedFile->getClientFilename(),
            $uploadedFile->getClientMediaType()
        );
    }

    /**
     * @return StreamInterface
     */
    public function getStream()
    {
        if (is_null($this->content)) {
            $cacheResult = $this->contentCache->get($this->getIdentifier());
            if ($cacheResult === false) {
                throw new \Exception('file content not found');
            } else {
                $this->content = $cacheResult;
            }
        }
        return $this->streamFactory->createStream($this->content);
    }

    public function moveTo($targetPath)
    {
        throw new \Exception('moveTo is not implemented');
    }

    /**
     * @return int|null
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return int
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string|null
     */
    public function getClientFilename()
    {
        return $this->clientFilename;
    }

    /**
     * @return string|null
     */
    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->identifier;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        // content is not serialized
        $this->content = null;
        return ['identifier', 'size', 'error', 'clientFilename', 'clientMediaType'];
    }

    /**
     * Mainly for debugging
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'identifier' => $this->getIdentifier(),
            'size' => $this->getSize(),
            'error' => $this->getError(),
            'clientFilename' => $this->getClientFilename(),
            'clientMediaType' => $this->getClientMediaType()
        ];
    }
}
