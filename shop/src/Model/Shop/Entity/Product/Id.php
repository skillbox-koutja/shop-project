<?php

declare(strict_types=1);

namespace App\Model\Shop\Entity\Product;

use Webmozart\Assert\Assert;

class Id
{
    public const SEQUENCE = 's_shop_product';
    private int $value;

    public function __construct(int $value)
    {
        Assert::notEmpty($value);
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }
}
