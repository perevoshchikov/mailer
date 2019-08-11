<?php

namespace Anper\Mailer\Tests\Event;

use Anper\Mailer\Event\BeforeSendEvent;
use Anper\Mailer\Message\Message;
use PHPUnit\Framework\TestCase;

class BeforeSendEventTest extends TestCase
{
    public function testSetMessageFromConstructor()
    {
        $message = new Message('test');

        $event = new BeforeSendEvent($message);

        $this->assertEquals($event->getMessage(), $message);
        $this->assertEquals($event->getMessage()->getId(), $message->getId());
    }

    public function testSetMessageFromSetter()
    {
        $message1 = new Message('test1');
        $message2 = new Message('test2');

        $event = new BeforeSendEvent($message1);
        $event->setMessage($message2);

        $this->assertEquals($event->getMessage(), $message2);
        $this->assertEquals($event->getMessage()->getId(), $message2->getId());
    }

    public function testSendStop()
    {
        $event = new BeforeSendEvent(new Message('test'));

        $this->assertFalse($event->isSendStopped());

        $event->stopSend();

        $this->assertTrue($event->isSendStopped());
    }
}
