<?php

namespace Anper\Mailer\Storage;

use Anper\Mailer\Exception\Exception;
use Anper\Mailer\Exception\NotFoundException;

class AggregateStorage implements StorageInterface
{
    /**
     * @var StorageInterface[]
     */
    protected $storage = [];

    /**
     * @var array
     */
    protected $map = [];

    /**
     * @param array $storage
     *
     * @throws Exception
     */
    public function __construct(array $storage = [])
    {
        foreach ($storage as $st) {
            if ($st instanceof StorageInterface) {
                $this->addStorage($st);
            } else {
                throw new Exception(sprintf(
                    'Expected storage instance of %s, given %s',
                    StorageInterface::class,
                    \is_object($st) ? \get_class($st) : \gettype($st)
                ));
            }
        }
    }

    /**
     * @return StorageInterface[]
     */
    public function getStorage(): array
    {
        return $this->storage;
    }

    /**
     * @param StorageInterface $storage
     * @return AggregateStorage
     */
    public function addStorage(StorageInterface $storage): self
    {
        if (\in_array($storage, $this->storage)) {
            return $this;
        }

        $this->storage[] = $storage;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function fetch(string $id, array $context = []): array
    {
        if (isset($this->map[$id])) {
            return $this->map[$id]->fetch($id, $context);
        }

        foreach ($this->getStorage() as $storage) {
            if ($storage->has($id)) {
                $this->map[$id] = $storage;

                return $storage->fetch($id, $context);
            }
        }

        throw new NotFoundException();
    }

    /**
     * @inheritdoc
     */
    public function has(string $id): bool
    {
        if (isset($this->map[$id])) {
            return true;
        }

        foreach ($this->storage as $storage) {
            if ($storage->has($id)) {
                $this->map[$id] = $storage;

                return true;
            }
        }

        return false;
    }
}
