<?php

namespace App\ReadModel\Shop\Product\Sorter;

class Sorter extends \App\ReadModel\Shared\Sorter
{
    public const FIELD_TITLE = 'title';
    public const FIELD_PRICE = 'price';

    protected static function allowFields(): array
    {
        return ['id', self::FIELD_TITLE, self::FIELD_PRICE];
    }

    public static function default()
    {
        return new self('id', self::SORT_DESC);
    }
}
