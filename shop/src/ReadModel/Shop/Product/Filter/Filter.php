<?php

namespace App\ReadModel\Shop\Product\Filter;

class Filter
{
    public ?string $title;
    public int $min;
    public int $max;
    public bool $new = false;
    public bool $sale = false;
    /** @var string[] */
    public $categories = [];
}
