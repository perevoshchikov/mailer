<?php

namespace Anper\Mailer\Tests\Storage;

use Anper\Mailer\Exception\NotFoundException;
use Anper\Mailer\Storage\MemoryStorage;
use Anper\Mailer\Storage\StorageInterface;
use PHPUnit\Framework\TestCase;

class MemoryStorageTest extends TestCase
{
    /**
     * @var array
     */
    protected $messages= [
        'foo' => [
            'subject' => 'Hello!',
        ],
    ];

    /**
     * @return StorageInterface
     */
    protected function getStorage(): StorageInterface
    {
        return new MemoryStorage($this->messages);
    }

    public function testHas()
    {
        $this->assertTrue($this->getStorage()->has('foo'));
    }

    public function testNotHas()
    {
        $this->assertFalse($this->getStorage()->has('bar'));
    }

    public function testFetch()
    {
        $message = $this->getStorage()->fetch('foo');

        $this->assertEquals($this->messages['foo'], $message);
    }

    public function testNotFound()
    {
        $this->expectException(NotFoundException::class);

        $this->getStorage()->fetch('bar');
    }
}
