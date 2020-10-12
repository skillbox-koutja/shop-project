<?php

declare(strict_types=1);

namespace App\Model\Shop\Entity\Category;

use Webmozart\Assert\Assert;

class Type
{
    public const NEW = 'new';
    public const SALE = 'sale';

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, self::existed());

        $this->name = $name;
    }

    public static function isExisted(string $name): bool
    {
        return false !== array_search($name, self::existed(), true);
    }

    public static function existed(): array
    {
        return [
            self::NEW,
            self::SALE,
        ];
    }

    public static function new(): self
    {
        return new self(self::NEW);
    }

    public static function sale(): self
    {
        return new self(self::SALE);
    }

    public function isEqual(self $other): bool
    {
        return $this->getName() === $other->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isNew(): bool
    {
        return $this->name === self::NEW;
    }

    public function isSale(): bool
    {
        return $this->name === self::SALE;
    }

    public function __toString()
    {
        return $this->name;
    }
}
