<?php

namespace Anper\Mailer\Storage;

use Anper\Mailer\Exception\NotFoundException;

interface StorageInterface
{
    /**
     * @param string $id
     * @param array $context
     *
     * @return array
     * @throws NotFoundException
     */
    public function fetch(string $id, array $context = []): array;

    /**
     * @param string $id
     *
     * @return bool
     */
    public function has(string $id): bool;
}
