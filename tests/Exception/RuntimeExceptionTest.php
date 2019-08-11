<?php

namespace Anper\Mailer\Tests\Exception;

use Anper\Mailer\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class RuntimeExceptionTest extends TestCase
{
    public function testMessage()
    {
        $exception = new RuntimeException('test', 'Prev message');

        $this->assertEquals('[test] Prev message', $exception->getMessage());
    }
}
