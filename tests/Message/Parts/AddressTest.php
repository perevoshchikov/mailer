<?php

namespace Anper\Mailer\Tests\Message\Parts;

use Anper\Mailer\Exception\Exception;
use Anper\Mailer\Message\Parts\Address;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    public function testInvalidAddressInConsturctor()
    {
        $this->expectException(Exception::class);

        new Address('not_address');
    }

    public function testGetAddress()
    {
        $address = new Address('foo@bar.com');

        $this->assertEquals($address->getAddress(), 'foo@bar.com');
    }

    public function testGetName()
    {
        $address = new Address('foo@bar.com', 'Foo Bar');

        $this->assertEquals($address->getName(), 'Foo Bar');
    }

    public function testGetEmptyName()
    {
        $address = new Address('foo@bar.com');

        $this->assertNull($address->getName());
    }

    public function testCreateFromString()
    {
        $address = Address::createFromString('foo@bar.com');

        $this->assertEquals($address->getAddress(), 'foo@bar.com');
    }

    public function testCreateFromStringWithName()
    {
        $address = Address::createFromString('foo@bar.com <Foo Bar>');

        $this->assertEquals($address->getAddress(), 'foo@bar.com');
        $this->assertEquals($address->getName(), 'Foo Bar');
    }

    public function testCreateArray()
    {
        $addresses = Address::createArray([
            'foo@bar.com <Foo Bar>',
            'baz@bar.com',
        ]);

        $this->assertEquals([
            new Address('foo@bar.com', 'Foo Bar'),
            new Address('baz@bar.com')
        ], $addresses);
    }

    public function testCreateAsSelf()
    {
        $address = new Address('foo@bar.com');

        $created = Address::create($address);

        $this->assertEquals($address, $created);
    }

    public function testCreateAsString()
    {
        $address = Address::create('foo@bar.com');

        $this->assertEquals($address->getAddress(), 'foo@bar.com');
    }

    public function testCreateInvalidArgument()
    {
        $this->expectException(Exception::class);

        Address::create(new \DateTime());
    }
}
