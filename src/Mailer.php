<?php

namespace Anper\Mailer;

use Anper\Mailer\Exception\Exception;
use Anper\Mailer\Exception\RuntimeException;
use Anper\Mailer\Message\Message;
use Anper\Mailer\Message\SendableMessage;
use Anper\Mailer\Storage\EventDispatchStorage;
use Anper\Mailer\Transport\EventDispatchTransport;
use Anper\Mailer\Transport\TransportInterface;
use Anper\Mailer\Storage\StorageInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Mailer implements MailerInterface
{
    /**
     * @var EventDispatchTransport
     */
    protected $transport;

    /**
     * @var EventDispatchStorage
     */
    protected $storage;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param TransportInterface $transport
     * @param StorageInterface $storage
     * @param EventDispatcherInterface|null $dispatcher
     */
    public function __construct(
        TransportInterface $transport,
        StorageInterface $storage,
        EventDispatcherInterface $dispatcher = null
    ) {
        $this->dispatcher = $dispatcher ?? new EventDispatcher();
        $this->transport  = $this->resolveTransport($transport);
        $this->storage    = $this->resolveStorage($storage);
    }

    /**
     * @return TransportInterface
     */
    public function getTransport(): TransportInterface
    {
        return $this->transport->getTransport();
    }

    /**
     * @return StorageInterface
     */
    public function getStorage(): StorageInterface
    {
        return $this->storage->getStorage();
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    /**
     * @param string $id
     * @param array $context
     *
     * @return SendableMessage
     * @throws RuntimeException
     */
    public function get(string $id, array $context = []): SendableMessage
    {
        try {
            return $this->createMessage($id, $context);
        } catch (\Exception $e) {
            throw new RuntimeException($id, $e->getMessage(), $e);
        }
    }

    /**
     * @param string $id
     * @param array $context
     *
     * @return bool
     * @throws RuntimeException
     */
    public function send(string $id, array $context = []): bool
    {
        return $this->get($id, $context)->send();
    }

    /**
     * @param Message $message
     *
     * @return bool
     * @throws RuntimeException
     */
    public function sendMessage(Message $message): bool
    {
        try {
            return $this->transport->send($message);
        } catch (\Exception $e) {
            throw new RuntimeException($message->getId(), $e->getMessage(), $e);
        }
    }

    /**
     * @param string $id
     * @param array $context
     *
     * @return SendableMessage
     * @throws Exception
     */
    protected function createMessage(string $id, array $context): SendableMessage
    {
        $data = $this->storage->fetch($id, $context);

        $message = new SendableMessage($id, $this);
        $message->fill($data);

        return $message;
    }

    /**
     * @param TransportInterface $transport
     *
     * @return EventDispatchTransport
     */
    protected function resolveTransport(TransportInterface $transport): EventDispatchTransport
    {
        return $transport instanceof EventDispatchTransport
            ? $transport
            : new EventDispatchTransport($transport, $this->dispatcher);
    }

    /**
     * @param StorageInterface $storage
     *
     * @return EventDispatchStorage
     */
    protected function resolveStorage(StorageInterface $storage): EventDispatchStorage
    {
        return $storage instanceof EventDispatchStorage
            ? $storage
            : new EventDispatchStorage($storage, $this->dispatcher);
    }
}
