<?php

namespace Anper\Mailer\Tests\Message;

use Anper\Mailer\Mailer;
use Anper\Mailer\MailerInterface;
use Anper\Mailer\Message\SendableMessage;
use PHPUnit\Framework\TestCase;

class SendableMessageTest extends TestCase
{
    public function testSetAndGetMailer()
    {
        $mailer = $this->createMock(MailerInterface::class);
        $message = new SendableMessage('test', $mailer);

        $this->assertEquals($mailer, $message->getMailer());
    }

    public function testSend()
    {
        $mailer = $this->createMock(MailerInterface::class);
        $message = new SendableMessage('test', $mailer);

        $mailer->expects($this->once())
            ->method('sendMessage')
            ->with($message)
            ->willReturn(true);

        $mailer->expects($this->never())
            ->method('send');

        $this->assertTrue($message->send());
    }

    public function testSetMailer()
    {
        $mailer1 = $this->createMock(MailerInterface::class);
        $mailer2 = $this->createMock(Mailer::class);

        $message = new SendableMessage('test', $mailer1);

        $message->setMailer($mailer2);

        $this->assertEquals($mailer2, $message->getMailer());
        $this->assertNotEquals($mailer1, $message->getMailer());
    }
}
