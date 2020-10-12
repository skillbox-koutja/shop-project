<?php

declare(strict_types=1);

namespace App\Model\Shop\Entity\Product\Photo;

class Info
{
    private string $path;
    private string $title;
    private int $size;

    public function __construct(string $path, string $title, int $size)
    {
        $this->path = $path;
        $this->title = $title;
        $this->size = $size;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}
