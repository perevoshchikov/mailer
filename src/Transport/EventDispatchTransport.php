<?php

namespace Anper\Mailer\Transport;

use Anper\Mailer\Event\AfterSendEvent;
use Anper\Mailer\Event\BeforeSendEvent;
use Anper\Mailer\Message\Message;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventDispatchTransport implements TransportInterface
{
    /**
     * @var TransportInterface
     */
    protected $transport;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param TransportInterface $transport
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(TransportInterface $transport, EventDispatcherInterface $dispatcher)
    {
        $this->transport  = $transport;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return TransportInterface
     */
    public function getTransport(): TransportInterface
    {
        return $this->transport;
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
    public function send(Message $message): bool
    {
        /** @var BeforeSendEvent $event */
        $event = $this->getDispatcher()
            ->dispatch(new BeforeSendEvent($message));

        $result = !$event->isSendStopped() && $this->getTransport()
                ->send($event->getMessage());

        $this->getDispatcher()
            ->dispatch(new AfterSendEvent($event->getMessage(), $result));

        return $result;
    }
}
