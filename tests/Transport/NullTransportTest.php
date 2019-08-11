<?php

namespace Anper\Mailer\Tests\Transport;

use Anper\Mailer\Message\Message;
use Anper\Mailer\Transport\NullTransport;
use PHPUnit\Framework\TestCase;

class NullTransportTest extends TestCase
{
    public function testSend()
    {
        $message = $this->createMock(Message::class);

        $transport = new NullTransport();

        $this->assertTrue($transport->send($message));
    }
}
