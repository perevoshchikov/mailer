<?php

namespace Anper\Mailer\Event;

use Anper\Mailer\Message\Message;
use Symfony\Contracts\EventDispatcher\Event;

class AfterSendEvent extends Event
{
    /**
     * @var Message
     */
    protected $message;

    /**
     * @var bool
     */
    protected $result;

    /**
     * @param Message $message
     * @param bool $result
     */
    public function __construct(Message $message, bool $result)
    {
        $this->message = $message;
        $this->result = $result;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function getResult(): bool
    {
        return $this->result;
    }
}
