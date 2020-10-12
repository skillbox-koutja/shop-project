<?php

namespace App\Tests\Builder\Shop;


use App\Model\Shop\Entity\Category\Category;
use App\Model\Shop\Entity\Category\Id;
use App\Model\Shop\Entity\Category\Type;

class CategoryBuilder
{
    private Id $id;
    private string $title = '';
    private Type $type;
    private array $index;

    public function __construct()
    {
        $this->index = [];
    }

    public function new(): self
    {
        $clone = clone $this;
        $clone->type = Type::new();
        $clone->id = $this->categoryId($clone->type);
        $clone->title = 'test-category-new';

        return $clone;
    }

    public function sale(): self
    {
        $clone = clone $this;
        $clone->type = Type::sale();
        $clone->id = $this->categoryId($clone->type);
        $this->title = 'test-category-new';
        return $clone;
    }

    public function build(string $slug = null, string $title = null)
    {
        if (isset($slug, $title)) {
            return new Category(
                $this->categoryId($slug),
                $slug,
                $title
            );
        }
        return new Category(
            $this->id,
            (string) $this->type,
            $this->title
        );
    }

    private function categoryId(string $slug): Id
    {
        if (isset($this->index[$slug])) {
            $id = $this->index[$slug];
        } else {
            $id = Id::next();
            $this->index[$slug] = $id;
        }
        return $id;
    }
}
