<?php

namespace Anper\Mailer\Message;

use Anper\Mailer\MailerInterface;

class SendableMessage extends Message
{
    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * @param string $id
     * @param MailerInterface $mailer
     */
    public function __construct(string $id, MailerInterface $mailer)
    {
        parent::__construct($id);

        $this->mailer = $mailer;
    }

    /**
     * @return MailerInterface
     */
    public function getMailer(): MailerInterface
    {
        return $this->mailer;
    }

    /**
     * @param MailerInterface $mailer
     *
     * @return SendableMessage
     */
    public function setMailer(MailerInterface $mailer): self
    {
        $this->mailer = $mailer;

        return $this;
    }

    /**
     * @return bool
     */
    public function send(): bool
    {
        return $this->mailer->sendMessage($this);
    }
}
