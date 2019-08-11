<?php

namespace Anper\Mailer\Tests\Event;

use Anper\Mailer\Event\AfterSendEvent;
use Anper\Mailer\Message\Message;
use PHPUnit\Framework\TestCase;

class AfterSendEventTest extends TestCase
{
    public function testConstructor()
    {
        $message = new Message('test');

        $event = new AfterSendEvent($message, true);

        $this->assertEquals($event->getMessage(), $message);
        $this->assertEquals($event->getMessage()->getId(), $message->getId());
        $this->assertTrue($event->getResult());
    }
}
