<?php

namespace Anper\Mailer\Event;

use Anper\Mailer\Message\Message;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeSendEvent extends Event
{
    /**
     * @var Message
     */
    protected $message;

    /**
     * @var bool
     */
    protected $sendStopped = false;

    /**
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }

    /**
     * @param Message $message
     *
     * @return BeforeSendEvent
     */
    public function setMessage(Message $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSendStopped(): bool
    {
        return $this->sendStopped;
    }

    /**
     * @return BeforeSendEvent
     */
    public function stopSend(): self
    {
        $this->sendStopped = true;

        return $this;
    }
}
