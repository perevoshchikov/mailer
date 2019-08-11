<?php

namespace Anper\Mailer\Exception;

class RuntimeException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @param string $id
     * @param string $message
     * @param \Throwable|null $previous
     */
    public function __construct(string $id, string $message, \Throwable $previous = null)
    {
        parent::__construct('[' . $id . '] ' . $message, 0, $previous);
    }
}
