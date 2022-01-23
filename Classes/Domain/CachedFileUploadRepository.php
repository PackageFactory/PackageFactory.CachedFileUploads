<?php
declare(strict_types=1);

namespace PackageFactory\CachedFileUploads\Domain;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\QueryInterface;
use Neos\Flow\Persistence\QueryResultInterface;
use Neos\Flow\Persistence\RepositoryInterface;
use PackageFactory\CachedFileUploads\Domain\CachedFileUpload;
use Neos\Cache\Frontend\VariableFrontend;

/**
 * @Flow\Scope("singleton")
 */
class CachedFileUploadRepository implements RepositoryInterface
{
    /**
     * @Flow\Inject
     * @var VariableFrontend
     */
    protected $fileCache;

    public function getEntityClassName(): string
    {
        return CachedFileUpload::class;
    }

    /**
     * @param CachedFileUpload $object
     * @return void
     * @throws \Neos\Cache\Exception
     */
    public function add($object): void
    {
        $this->fileCache->set($object->getIdentifier(), $object);
    }

    /**
     * @param CachedFileUpload $object
     * @return void
     */
    public function remove($object): void
    {
        $this->fileCache->remove($object->getIdentifier());
    }

    /**
     * @return void
     */
    public function removeAll(): void
    {
        $this->fileCache->flush();
    }

    /**
     * @param CachedFileUpload $object
     * @return void
     * @throws \Neos\Cache\Exception
     */
    public function update($object): void
    {
        $this->fileCache->set($object->getIdentifier(), $object);
    }

    /**
     * @param $identifier
     * @return CachedFileUpload|null
     */
    public function findByIdentifier($identifier)
    {
        return $this->fileCache->get($identifier);
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
