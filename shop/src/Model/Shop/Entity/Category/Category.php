<?php

declare(strict_types=1);

namespace App\Model\Shop\Entity\Category;

use App\Model\Shop\Entity\Product\Product;
use Doctrine\Common\Collections\ArrayCollection;

class Category
{
    private Id $id;
    private string $slug;
    private string $title;
    /** @var Product[]|ArrayCollection */
    private $products;
    private int $version;

    public function __construct(Id $id, string $slug, string $title)
    {
        $this->id = $id;
        $this->slug = $slug;
        $this->title = $title;
        $this->products = new ArrayCollection();
    }

    public function edit(string $slug, string $title): void
    {
        $this->slug = $slug;
        $this->title = $title;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isNew(): bool
    {
        if (Type::isExisted($this->slug)) {
            return $this->type()->isEqual(Type::new());
        }

        return false;
    }

    public function isSale(): bool
    {
        if (Type::isExisted($this->slug)) {
            return $this->type()->isEqual(Type::sale());
        }

        return false;
    }

    private function type(): Type
    {
        return new Type($this->slug);
    }
}
