<?php

namespace Anper\Mailer\Tests\Message\Parts;

use Anper\Mailer\Exception\Exception;
use Anper\Mailer\Message\Parts\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testInvalidAddressInConsturctor()
    {
        $this->expectException(Exception::class);

        new File('not_file');
    }

    public function testGetFile()
    {
        $file = new File(__FILE__);

        $this->assertEquals($file->getFile(), __FILE__);
    }

    public function testGetName()
    {
        $file = new File(__FILE__, 'file.php');

        $this->assertEquals($file->getName(), 'file.php');
    }

    public function testGetEmptyName()
    {
        $file = new File(__FILE__);

        $this->assertNull($file->getName());
    }

    public function testCreateFromString()
    {
        $address = File::createFromString(__FILE__);

        $this->assertEquals($address->getFile(), __FILE__);
    }

    public function testCreateFromStringWithName()
    {
        $file = File::createFromString(__FILE__ . ' <file.php>');

        $this->assertEquals($file->getFile(), __FILE__);
        $this->assertEquals($file->getName(), 'file.php');
    }

    public function testCreateArray()
    {
        $files = File::createArray([
            __FILE__ . ' <file.php>',
            __FILE__,
        ]);

        $this->assertEquals([
            new File(__FILE__, 'file.php'),
            new File(__FILE__)
        ], $files);
    }

    public function testCreateAsSelf()
    {
        $file = new File(__FILE__);

        $created = File::create($file);

        $this->assertEquals($file, $created);
    }

    public function testCreateAsString()
    {
        $file = File::create(__FILE__);

        $this->assertEquals($file->getFile(), __FILE__);
    }

    public function testCreateInvalidArgument()
    {
        $this->expectException(Exception::class);

        File::create(new \DateTime());
    }
}
