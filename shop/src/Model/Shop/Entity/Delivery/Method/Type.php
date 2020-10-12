<?php

namespace App\Model\Shop\Entity\Delivery\Method;

use Webmozart\Assert\Assert;

class Type
{
    public const PICKUP = 'pickup';
    public const COURIER = 'courier';
    public const DEFAULT = self::PICKUP;

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::PICKUP,
            self::COURIER,
        ]);

        $this->name = $name;
    }

    public function isEqual(self $other): bool
    {
        return $this->getName() === $other->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isPickup(): bool
    {
        return $this->name === self::PICKUP;
    }

    public function isCourier(): bool
    {
        return $this->name === self::COURIER;
    }

    public function __toString()
    {
        return $this->name;
    }
}

