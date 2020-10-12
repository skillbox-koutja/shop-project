<?php

namespace App\Tests\Builder\Shop;

use App\Model\Shop\Entity\Category\Category;
use App\Model\Shop\Entity\Product\Id;
use App\Model\Shop\Entity\Product\Product;

class ProductBuilder
{
    private Id $id;
    private string $title;
    private int $price;
    /** @var Category[]|iterable */
    private $categories;

    public function __construct()
    {
        $this->id = new Id(1);
        $this->title = 'test-product';
        $this->price = 100;
        $this->categories = [];
    }

    public function withId(Id $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function withCategories(iterable $categories)
    {
        $clone = clone $this;
        $clone->categories = $categories;
        return $clone;
    }

    public function withPrice(int $price)
    {
        $clone = clone $this;
        $clone->price = $price;
        return $clone;
    }

    public function build(): Product
    {
        $product = new Product(
            $this->id,
            $this->title,
            $this->price
        );
        if (!empty($this->categories)) {
            foreach ($this->categories as $category) {
                $product->assignCategory($category);
            }
        }

        return $product;
    }
}
