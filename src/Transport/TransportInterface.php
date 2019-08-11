<?php

namespace Anper\Mailer\Transport;

use Anper\Mailer\Message\Message;

interface TransportInterface
{
    /**
     * @param Message $message
     *
     * @return bool
     */
    public function send(Message $message): bool;
}
