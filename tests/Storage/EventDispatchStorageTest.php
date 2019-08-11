<?php

namespace Anper\Mailer\Tests\Storage;

use Anper\Mailer\Event\AfterFetchEvent;
use Anper\Mailer\Event\BeforeFetchEvent;
use Anper\Mailer\Storage\EventDispatchStorage;
use Anper\Mailer\Storage\StorageInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventDispatchStorageTest extends TestCase
{
    public function testSetFromConstructor()
    {
        $storage = $this->createMock(StorageInterface::class);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $eds = new EventDispatchStorage($storage, $dispatcher);

        $this->assertEquals($storage, $eds->getStorage());
        $this->assertEquals($dispatcher, $eds->getDispatcher());
    }

    /**
     * @return array
     */
    public function hasProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @dataProvider hasProvider
     * @param bool $has
     */
    public function testHas(bool $has)
    {
        $message = 'test';

        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->once())
            ->method('has')
            ->with($message)
            ->willReturn($has);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $eds = new EventDispatchStorage($storage, $dispatcher);

        $this->assertEquals($has, $eds->has($message));
    }

    public function testFetch()
    {
        $message  = 'test';
        $context1 = ['subject' => 'foo1']; // given context
        $context2 = ['subject' => 'foo2']; // context changed by before event
        $data1    = ['subject' => 'bar1']; // given data
        $data2    = ['subject' => 'bar2']; // data changed by after event

        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->once())
            ->method('fetch')
            ->with($message, $context2)
            ->willReturn($data1);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $dispatcher->expects($this->at(0))
            ->method('dispatch')
            ->with(new BeforeFetchEvent($message, $context1))
            ->willReturn(new BeforeFetchEvent($message, $context2));

        $dispatcher->expects($this->at(1))
            ->method('dispatch')
            ->with(new AfterFetchEvent($message, $data1))
            ->willReturn(new AfterFetchEvent($message, $data2));

        $eds = new EventDispatchStorage($storage, $dispatcher);

        $this->assertEquals($data2, $eds->fetch($message, $context1));
    }
}
