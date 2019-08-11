<?php

namespace Anper\Mailer\Tests\Storage;

use Anper\Mailer\Exception\Exception;
use Anper\Mailer\Exception\NotFoundException;
use Anper\Mailer\Storage\AggregateStorage;
use Anper\Mailer\Storage\StorageInterface;
use PHPUnit\Framework\TestCase;

class AggregateStorageTest extends TestCase
{
    public function testSetInvalidStorageFromConstructor()
    {
        $this->expectException(Exception::class);

        new AggregateStorage([new \DateTime()]);
    }

    public function testAddStorage()
    {
        $aggregate = new AggregateStorage();

        $storage = $this->createMock(StorageInterface::class);

        $aggregate->addStorage($storage);

        $this->assertContains($storage, $aggregate->getStorage());
        $this->assertCount(1, $aggregate->getStorage());

        // test double add
        $aggregate->addStorage($storage);
        $this->assertCount(1, $aggregate->getStorage());
    }

    /**
     * @return array
     */
    public function providerHas(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @dataProvider providerHas
     * @param bool $has
     */
    public function testHas(bool $has)
    {
        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->atMost(1))
            ->method('has')
            ->with('foo')
            ->willReturn($has);

        $aggregate = new AggregateStorage([$storage]);

        $this->assertEquals($has, $aggregate->has('foo'));
    }

    public function testHasWithMappedStorage()
    {
        $storage1 = $this->createMock(StorageInterface::class);
        $storage1->expects($this->once())
            ->method('has')
            ->with('foo')
            ->willReturn(true);

        $storage2 = $this->createMock(StorageInterface::class);

        $storage2->expects($this->once())
            ->method('has')
            ->with('foo')
            ->willReturn(false);

        $aggregate = new AggregateStorage([$storage2, $storage1]);

        $this->assertTrue($aggregate->has('foo'));
        $this->assertTrue($aggregate->has('foo')); // from mapped storage
    }

    public function testFetch()
    {
        $data  = ['subject' => 'hello'];
        $params = ['foo' => 'bar'];

        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->atLeast(1))
            ->method('fetch')
            ->with('foo', $params)
            ->willReturn($data);

        $storage->expects($this->once())
            ->method('has')
            ->with('foo')
            ->willReturn(true);

        $aggregate = new AggregateStorage([$storage]);

        $this->assertEquals($data, $aggregate->fetch('foo', $params));
    }

    public function testFetchWithMappedStorage()
    {
        $data  = ['subject' => 'hello'];
        $params = ['foo' => 'bar'];

        $storage1 = $this->createMock(StorageInterface::class);
        $storage1->expects($this->atLeast(1))
            ->method('fetch')
            ->with('foo', $params)
            ->willReturn($data);

        $storage1->expects($this->once())
            ->method('has')
            ->with('foo')
            ->willReturn(true);

        $storage2 = $this->createMock(StorageInterface::class);
        $storage2->expects($this->never())
            ->method('fetch');

        $storage2->expects($this->once())
            ->method('has')
            ->with('foo')
            ->willReturn(false);

        $aggregate = new AggregateStorage([$storage2, $storage1]);

        $this->assertEquals($data, $aggregate->fetch('foo', $params));
        $this->assertEquals($data, $aggregate->fetch('foo', $params)); // from mapped storage
    }

    public function testNotFound()
    {
        $this->expectException(NotFoundException::class);

        $aggregate = new AggregateStorage([]);
        $aggregate->fetch('test');
    }
}
