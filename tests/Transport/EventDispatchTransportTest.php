<?php

namespace Anper\Mailer\Tests\Transport;

use Anper\Mailer\Event\AfterSendEvent;
use Anper\Mailer\Event\BeforeSendEvent;
use Anper\Mailer\Message\Message;
use Anper\Mailer\Transport\EventDispatchTransport;
use Anper\Mailer\Transport\TransportInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventDispatchTransportTest extends TestCase
{
    public function testSetFromConstructor()
    {
        $transport = $this->createMock(TransportInterface::class);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $edt = new EventDispatchTransport($transport, $dispatcher);

        $this->assertEquals($transport, $edt->getTransport());
        $this->assertEquals($dispatcher, $edt->getDispatcher());
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            // Change message by before event with true result
            [true, $this->before('tmp')],
            // Default send flow with true result
            [true, $this->before()],
            // Change message by before event with false result
            [false, $this->before('tmp')],
            // Default send flow with false result
            [false, $this->before()],
            // Stop send message by before event
            [false, $this->before('test', true)],
            // Stop send and change message by before event
            [false, $this->before('tmp', true)],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @param bool $result
     * @param BeforeSendEvent $beforeSendEvent
     */
    public function testSend(bool $result, BeforeSendEvent $beforeSendEvent)
    {
        $message = new Message('test');

        $transport = $this->createMock(TransportInterface::class);
        $transport->expects($beforeSendEvent->isSendStopped() ? $this->never() : $this->once())
            ->method('send')
            ->with($beforeSendEvent->getMessage())
            ->willReturn($result);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $dispatcher->expects($this->at(0))
            ->method('dispatch')
            ->with(new BeforeSendEvent($message))
            ->willReturn($beforeSendEvent);

        $dispatcher->expects($this->at(1))
            ->method('dispatch')
            ->with(new AfterSendEvent($beforeSendEvent->getMessage(), $result));

        $edt = new EventDispatchTransport($transport, $dispatcher);

        $this->assertEquals($result, $edt->send($message));
    }

    /**
     * @param string $message
     * @param bool $stop
     *
     * @return BeforeSendEvent
     */
    protected function before(string $message = 'test', bool $stop = false): BeforeSendEvent
    {
        $event = new BeforeSendEvent(new Message($message));

        if ($stop) {
            $event->stopSend();
        }

        return $event;
    }
}
