<?php

namespace App\Model\Shop\Entity\Order;

use Webmozart\Assert\Assert;

class Status
{
    public const UNDONE = 'undone';
    public const DONE = 'done';

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::UNDONE,
            self::DONE,
        ]);

        $this->name = $name;
    }

    public static function undone(): self
    {
        return new self(self::UNDONE);
    }

    public static function done(): self
    {
        return new self(self::DONE);
    }

    public function isEqual(self $other): bool
    {
        return $this->getName() === $other->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isDone(): bool
    {
        return $this->name === self::DONE;
    }

    public function isUndone(): bool
    {
        return $this->name === self::UNDONE;
    }
}
