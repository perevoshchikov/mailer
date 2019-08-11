<?php

namespace Anper\Mailer\Storage;

use Anper\Mailer\Exception\NotFoundException;

class MemoryStorage implements StorageInterface
{
    /**
     * @var array
     */
    protected $messages;

    /**
     * @param array $messages
     */
    public function __construct(array $messages)
    {
        $this->messages = $messages;
    }

    /**
     * @inheritdoc
     */
    public function fetch(string $id, array $context = []): array
    {
        if ($this->has($id)) {
            return (array) $this->messages[$id];
        }

        throw new NotFoundException($id);
    }

    /**
     * @inheritdoc
     */
    public function has(string $id): bool
    {
        return isset($this->messages[$id]);
    }
}
