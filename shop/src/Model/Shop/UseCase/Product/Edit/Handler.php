<?php

declare(strict_types=1);

namespace App\Model\Shop\UseCase\Product\Edit;

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
        $this->products = $products;
        $this->photos = $photos;
        $this->categories = $categories;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $product = $this->products->get(new Product\Id($command->id));
        if (isset($command->photo)) {
            $product->removePhoto($this->photos);
            $this->flusher->flush($product);

            $photo = new Product\Photo\Photo(
                $product,
                Product\Photo\Id::next(),
                $this->photoInfo($command->photo)
            );
            $product->changePhoto($photo);
        }
        if (isset($command->title)) {
            $product->changeTitle($command->title);
        }
        if (isset($command->price)) {
            $product->changePrice($command->price);
        }

        $types = $command->categories;
        if ($command->new) {
            $types[] = Category\Type::new();
        }
        if ($command->sale) {
            $types[] = Category\Type::sale();
        }
        $categories = $this->categories->getByTypes($types);
        $product->updateCategories($categories);

        $this->flusher->flush($product);
    }

    private function photoInfo(PhotoInfo $info)
    {
        return new Product\Photo\Info(
            $info->path,
            $info->title,
            $info->size,
        );
    }
}

