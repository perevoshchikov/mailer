<?php

namespace Anper\Mailer\Transport;

use Anper\Mailer\Message\Message;

class NullTransport implements TransportInterface
{
    /**
     * @inheritDoc
     */
    public function send(Message $message): bool
    {
        return true;
    }
}
