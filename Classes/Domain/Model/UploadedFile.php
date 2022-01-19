<?php
declare(strict_types=1);

namespace PackageFactory\CachedFileUploads\Domain\Model;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Utility\Algorithms;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\StreamFactoryInterface;

class UploadedFile implements UploadedFileInterface
{
    /**
     * @Flow\Inject
     * @var StreamFactoryInterface
     */
    protected $streamFactory;

    /**
     * @var string
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
    protected $errorStatus;

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
     * @param string $identifier
     * @param string $content
     * @param int $size
     * @param int $errorStatus
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     */
    protected function __construct(string $identifier, string $content, int $size, int $errorStatus, string $clientFilename = null, string $clientMediaType = null)
    {
        $this->identifier = $identifier;
        $this->content = $content;
        $this->size = $size;
        $this->errorStatus = $errorStatus;
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;
    }

    /**
     * @param UploadedFileInterface $uploadedFile
     * @return UploadedFile
     */
    public static function fromUploadedFile(UploadedFileInterface $uploadedFile): self
    {
        return new static(
            Algorithms::generateUUID(),
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
        return $this->streamFactory->createStream($this->content);
    }

    public function moveTo($targetPath)
    {
        throw new \Exception("moveTo is not implemented");
    }

    /**
     * @return int|null
     */
    public function getSize()
    {
        return $this->errorStatus;
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
}
