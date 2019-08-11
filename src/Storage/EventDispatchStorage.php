<?php

namespace Anper\Mailer\Storage;

use Anper\Mailer\Event\AfterFetchEvent;
use Anper\Mailer\Event\BeforeFetchEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventDispatchStorage implements StorageInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param StorageInterface $storage
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(StorageInterface $storage, EventDispatcherInterface $dispatcher)
    {
        $this->storage    = $storage;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return StorageInterface
     */
    public function getStorage(): StorageInterface
    {
        return $this->storage;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    /**
     * @inheritDoc
     */
    public function fetch(string $id, array $context = []): array
    {
        /** @var BeforeFetchEvent $before */
        $before = $this->getDispatcher()
            ->dispatch(new BeforeFetchEvent($id, $context));

        $data = $this->getStorage()
            ->fetch($id, $before->getArguments());

        /** @var AfterFetchEvent $after */
        $after = $this->getDispatcher()
            ->dispatch(new AfterFetchEvent($id, $data));

        return $after->getArguments();
    }

    /**
     * @inheritDoc
     */
    public function has(string $id): bool
    {
        return $this->getStorage()->has($id);
    }
}
