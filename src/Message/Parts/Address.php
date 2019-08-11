<?php

namespace Anper\Mailer\Message\Parts;

use Anper\Mailer\Exception\Exception;

class Address
{
    /**
     * @var string
     */
    protected $address;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @param string|Address $address
     *
     * @return Address
     * @throws Exception
     */
    public static function create($address): self
    {
        if ($address instanceof static) {
            return $address;
        }

        if (\is_string($address)) {
            return static::createFromString($address);
        }

        throw new Exception(sprintf(
            'Address must be string or instance of "%s", given "%s"',
            static::class,
            \is_object($address) ? \get_class($address) : \gettype($address)
        ));
    }

    /**
     * @param Address[]|string[] $addresses
     *
     * @return Address[]
     * @throws Exception
     */
    public static function createArray(array $addresses): array
    {
        return \array_map(function ($address) {
            return static::create($address);
        }, $addresses);
    }

    /**
     * @param string $address
     *
     * @return Address
     * @throws Exception
     */
    public static function createFromString(string $address): self
    {
        if (\preg_match('/^(?P<address>.+)\s<(?P<name>.+)>$/', $address, $matches)) {
            return new static($matches['address'], $matches['name']);
        }

        return new static($address);
    }

    /**
     * @param string $address
     * @param string|null $name
     *
     * @throws Exception
     */
    public function __construct(string $address, string $name = null)
    {
        if (!\filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid address format '$address'");
        }

        $this->address = $address;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
