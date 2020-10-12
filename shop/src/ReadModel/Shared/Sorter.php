<?php

namespace App\ReadModel\Shared;

use Webmozart\Assert\Assert;

abstract class Sorter
{
    public const SORT_ASC = 'asc';
    public const SORT_DESC = 'desc';

    public string $field;
    public string $order;
    public int $orderId;

    public function __construct(string $field, string $order)
    {
        Assert::oneOf($field, static::allowFields());
        Assert::oneOf($order, [self::SORT_ASC, self::SORT_DESC]);
        $this->field = $field;
        $this->order = $order;
        $this->orderId = self::SORT_ASC === $order? SORT_ASC : SORT_DESC;
    }

    abstract protected static function allowFields(): array;
}
