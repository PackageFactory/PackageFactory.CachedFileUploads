<?php
declare(strict_types=1);

namespace PackageFactory\CachedFileUploads\Domain\Repository;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\QueryInterface;
use Neos\Flow\Persistence\QueryResultInterface;
use Neos\Flow\Persistence\RepositoryInterface;
use PackageFactory\CachedFileUploads\Domain\Model\UploadedFile;
use Neos\Cache\Frontend\VariableFrontend;

/**
 * @Flow\Scope("singleton")
 */
class UploadedFileRepository implements RepositoryInterface
{
    /**
     * @Flow\Inject
     * @var VariableFrontend
     */
    protected $uploadedFileCache;

    public function getEntityClassName(): string
    {
        return UploadedFile::class;
    }

    /**
     * @param UploadedFile $object
     * @return void
     * @throws \Neos\Cache\Exception
     */
    public function add($object): void
    {
        $this->uploadedFileCache->set($object->getIdentifier(), $object);
    }

    /**
     * @param UploadedFile $object
     * @return void
     */
    public function remove($object): void
    {
        $this->uploadedFileCache->remove($object->getIdentifier());
    }

    /**
     * @return void
     */
    public function removeAll(): void
    {
        $this->uploadedFileCache->flush();
    }

    /**
     * @param UploadedFile $object
     * @return void
     * @throws \Neos\Cache\Exception
     */
    public function update($object): void
    {
        $this->uploadedFileCache->set($object->getIdentifier(), $object);
    }

    /**
     * @param $identifier
     * @return UploadedFile|null
     */
    public function findByIdentifier($identifier)
    {
        return $this->uploadedFileCache->get($identifier);
    }

    public function findAll(): QueryResultInterface
    {
        throw new \Exception("findAll is not implemented");
    }

    public function createQuery(): QueryInterface
    {
        throw new \Exception("createQuery is not implemented");
    }

    public function countAll(): int
    {
        throw new \Exception("countAll is not implemented");
    }

    public function setDefaultOrderings(array $defaultOrderings): void
    {
        throw new \Exception("setDefaultOrderings is not implemented");
    }

    public function __call($method, $arguments)
    {
        throw new \Exception($method . " is not implemented");
    }
}
