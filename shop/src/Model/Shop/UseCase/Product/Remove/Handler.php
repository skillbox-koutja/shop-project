<?php

declare(strict_types=1);

namespace App\Model\Shop\UseCase\Product\Remove;

use App\Model\Flusher;
use App\Model\Shop\Entity\Product;

class Handler
{
    private Flusher $flusher;
    private Product\ProductRepository $products;

    public function __construct(
        Product\ProductRepository $products,
        Flusher $flusher
    )
    {
        $this->products = $products;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $product = $this->products->get(new Product\Id($command->id));

        $this->products->remove($product);

        $this->flusher->flush($product);
    }
}

