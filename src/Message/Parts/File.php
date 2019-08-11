<?php

namespace Anper\Mailer\Message\Parts;

use Anper\Mailer\Exception\Exception;

class File
{
    /**
     * @var string
     */
    protected $file;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @param string|File $file
     *
     * @return File
     * @throws Exception
     */
    public static function create($file): self
    {
        if ($file instanceof static) {
            return $file;
        }

        if (\is_string($file)) {
            return static::createFromString($file);
        }

        throw new Exception(sprintf(
            'File must be string or instance of "%s", given "%s"',
            static::class,
            \is_object($file) ? \get_class($file) : \gettype($file)
        ));
    }

    /**
     * @param File[]|string[] $files
     *
     * @return File[]
     * @throws Exception
     */
    public static function createArray(array $files): array
    {
        return \array_map(function ($file) {
            return static::create($file);
        }, $files);
    }

    /**
     * @param string $file
     *
     * @return File
     * @throws Exception
     */
    public static function createFromString(string $file): self
    {
        if (\preg_match('/^(?P<file>.+)\s<(?P<name>.+)>$/', $file, $matches)) {
            return new static($matches['file'], $matches['name'] ?? null);
        }

        return new static($file);
    }

    /**
     * @param string $file
     * @param string|null $name
     *
     * @throws Exception
     */
    public function __construct(string $file, string $name = null)
    {
        if (!\is_file($file)) {
            throw new Exception("File '$file' not found");
        }

        $this->file = $file;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
