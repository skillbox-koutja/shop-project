<?php

namespace App\Model\Shop\Entity\Product\Event;

use App\Model\EventInterface;
use App\Model\Shop\Entity\Product;

class ProductPhotoRemoved implements EventInterface
{
    public Product\Id $productId;
    public Product\Photo\Info $info;

    public function __construct(
        Product\Id $productId,
        Product\Photo\Info $info
    )
    {
        $this->productId = $productId;
        $this->info = $info;
    }

}
