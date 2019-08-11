<?php

namespace Anper\Mailer\Tests;

use Anper\Mailer\Event\AfterFetchEvent;
use Anper\Mailer\Event\AfterSendEvent;
use Anper\Mailer\Event\BeforeFetchEvent;
use Anper\Mailer\Event\BeforeSendEvent;
use Anper\Mailer\Exception\RuntimeException;
use Anper\Mailer\Mailer;
use Anper\Mailer\Message\Message;
use Anper\Mailer\Storage\EventDispatchStorage;
use Anper\Mailer\Storage\StorageInterface;
use Anper\Mailer\Transport\EventDispatchTransport;
use Anper\Mailer\Transport\TransportInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MailerTest extends TestCase
{
    public function testGetTransport()
    {
        $transport = $this->createMock(TransportInterface::class);
        $storage = $this->createMock(StorageInterface::class);

        $mailer = new Mailer($transport, $storage);

        $this->assertEquals($transport, $mailer->getTransport());
    }

    public function testGetStorage()
    {
        $transport = $this->createMock(TransportInterface::class);
        $storage = $this->createMock(StorageInterface::class);

        $mailer = new Mailer($transport, $storage);

        $this->assertEquals($storage, $mailer->getStorage());
    }

    public function testGetDefaultDispatcher()
    {
        $transport = $this->createMock(TransportInterface::class);
        $storage = $this->createMock(StorageInterface::class);

        $mailer = new Mailer($transport, $storage);

        $this->assertInstanceOf(EventDispatcher::class, $mailer->getDispatcher());
    }

    public function testGetDispatcher()
    {
        $transport = $this->createMock(TransportInterface::class);
        $storage = $this->createMock(StorageInterface::class);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $mailer = new Mailer($transport, $storage, $dispatcher);

        $this->assertEquals($dispatcher, $mailer->getDispatcher());
    }

    public function testGet()
    {
        $id      = 'test';
        $context = ['foo' => 'bar'];
        $data    = ['subject' => 'hello'];

        $transport = $this->createMock(TransportInterface::class);

        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->once())
            ->method('fetch')
            ->with($id, $context)
            ->willReturn($data);

        $mailer = new Mailer($transport, $storage);

        $message = $mailer->get($id, $context);

        $this->assertEquals($data['subject'], $message->getSubject());
        $this->assertEquals($id, $message->getId());
    }

    public function testInvalidGet()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('[test] exception message');

        $transport = $this->createMock(TransportInterface::class);
        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->once())
            ->method('fetch')
            ->willThrowException(new \Exception('exception message'));

        $mailer = new Mailer($transport, $storage);
        $mailer->get('test');
    }

    /**
     * @return array
     */
    public function sendMessageResultProvider(): array
    {
        return [
            [true],
            [false]
        ];
    }

    /**
     * @dataProvider sendMessageResultProvider
     * @param bool $result
     */
    public function testSendMessage(bool $result)
    {
        $message = new Message('test');

        $transport = $this->createMock(TransportInterface::class);
        $transport->expects($this->once())
            ->method('send')
            ->with($message)
            ->willReturn($result);

        $storage = $this->createMock(StorageInterface::class);

        $mailer = new Mailer($transport, $storage);

        $this->assertEquals($result, $mailer->sendMessage($message));
    }


    public function testInvalidSendMessage()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('[test] transport exception');

        $message = new Message('test');

        $transport = $this->createMock(TransportInterface::class);
        $transport->expects($this->once())
            ->method('send')
            ->with($message)
            ->willThrowException(new \Exception('transport exception'));

        $storage = $this->createMock(StorageInterface::class);

        $mailer = new Mailer($transport, $storage);

        $this->assertTrue($mailer->sendMessage($message));
    }

    /**
     * @return array
     */
    public function sendResultProvider(): array
    {
        return [
            [true],
            [false]
        ];
    }

    /**
     * @dataProvider sendResultProvider
     * @param bool $result
     */
    public function testSend(bool $result)
    {
        $id      = 'test';
        $context = ['foo' => 'bar'];
        $data    = ['subject' => 'hello'];

        $transport = $this->createMock(TransportInterface::class);
        $transport->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Message::class))
            ->willReturn($result);

        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->once())
            ->method('fetch')
            ->with($id, $context)
            ->willReturn($data);

        $mailer = new Mailer($transport, $storage);
        $this->assertEquals($result, $mailer->send($id, $context));
    }

    /**
     * @return array
     */
    public function eventProvider(): array
    {
        return [
            [BeforeSendEvent::class],
            [AfterSendEvent::class],
            [BeforeFetchEvent::class],
            [AfterFetchEvent::class],
        ];
    }

    /**
     * @dataProvider eventProvider
     * @param string $event
     */
    public function testEventCall(string $event)
    {
        $transport = $this->createMock(TransportInterface::class);

        $transport->expects($this->once())
            ->method('send')
            ->willReturn(true);

        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->once())
            ->method('fetch')
            ->willReturn([]);

        $called = false;

        $mailer = new Mailer($transport, $storage);
        $mailer->getDispatcher()
            ->addListener($event, function () use (&$called) {
                $called = true;
            });

        $mailer->send('test');

        $this->assertTrue($called);
    }

    public function testNotWrappedEventDispatchStorage()
    {
        $transport = $this->createMock(TransportInterface::class);
        $storage = $this->createMock(StorageInterface::class);

        $eventStorage = $this->createMock(EventDispatchStorage::class);
        $eventStorage->expects($this->once())
            ->method('getStorage')
            ->willReturn($storage);

        $mailer = new Mailer($transport, $eventStorage);
        $this->assertEquals($storage, $mailer->getStorage());
    }

    public function testNotWrappedEventDispatchTrasport()
    {
        $transport = $this->createMock(TransportInterface::class);
        $storage = $this->createMock(StorageInterface::class);

        $eventTransport = $this->createMock(EventDispatchTransport::class);
        $eventTransport->expects($this->once())
            ->method('getTransport')
            ->willReturn($transport);

        $mailer = new Mailer($eventTransport, $storage);
        $this->assertEquals($transport, $mailer->getTransport());
    }
}
