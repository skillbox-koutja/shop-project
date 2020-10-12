<?php

declare(strict_types=1);

namespace App\Model\Shop\UseCase\Product;

class PhotoInfo
{
    public string $path;
    public string $title;
    public int $size;

    public function __construct(string $path, string $title, int $size)
    {
        $this->path = $path;
        $this->title = $title;
        $this->size = $size;
    }
}
