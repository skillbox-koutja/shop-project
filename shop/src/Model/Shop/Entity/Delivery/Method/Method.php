<?php

declare(strict_types=1);

namespace App\Model\Shop\Entity\Delivery\Method;

class Method
{
    private Id $id;
    private Type $type;
    private string $title;
    private ?int $cost;
    private ?int $minPrice;

    public function __construct(
        Id $id,
        Type $type,
        string $title,
        ?int $cost = null,
        ?int $minPrice = null
    )
    {
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
        $this->cost = $cost;
        $this->minPrice = $minPrice;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function getMinPrice(): ?int
    {
        return $this->minPrice;
    }
}
