<?php

namespace Anper\Mailer;

use Anper\Mailer\Message\Message;

interface MailerInterface
{
    /**
     * @param string $id
     * @param array $context
     *
     * @return bool
     */
    public function send(string $id, array $context = []): bool;

    /**
     * @param Message $message
     *
     * @return bool
     */
    public function sendMessage(Message $message): bool;
}
