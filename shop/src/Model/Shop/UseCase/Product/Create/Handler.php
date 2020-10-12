<?php

declare(strict_types=1);

namespace App\Model\Shop\UseCase\Product\Create;

use App\Model\Flusher;
use App\Model\Shop\Entity\Category;
use App\Model\Shop\Entity\Product;
use App\Model\Shop\UseCase\Product\PhotoInfo;

class Handler
{
    private Flusher $flusher;
    private Product\ProductRepository $products;
    private Product\Photo\PhotoRepository $photos;
    private Category\CategoryRepository $categories;

    public function __construct(
        Product\ProductRepository $products,
        Product\Photo\PhotoRepository $photos,
        Category\CategoryRepository $categories,
        Flusher $flusher
    )
    {
        $this->flusher = $flusher;
        $this->products = $products;
        $this->photos = $photos;
        $this->categories = $categories;
    }

    public function handle(Command $command): void
    {
        $product = new Product\Product(
            $this->products->nextId(),
            $command->title,
            $command->price
        );
        $photo = new Product\Photo\Photo(
            $product,
            Product\Photo\Id::next(),
            $this->photoInfo($command->photo)
        );
        $product->changePhoto($photo);

        $types = $command->categories;
        if ($command->new) {
            $types[] = Category\Type::new();
        }
        if ($command->sale) {
            $types[] = Category\Type::sale();
        }
        $categories = $this->categories->getByTypes($types);
        foreach ($categories as $category) {
            $product->assignCategory($category);
        }
        $this->products->add($product);
        $this->flusher->flush($product);
    }

    private function photoInfo(PhotoInfo $photo)
    {
        return new Product\Photo\Info(
            $photo->path,
            $photo->title,
            $photo->size,
        );
    }
}

