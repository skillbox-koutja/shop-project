<?php

declare(strict_types=1);

namespace App\Model\Shop\UseCase\Product\Remove;

use App\Model\Shop\Entity\Product\Product;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromProduct(Product $product)
    {
        return new self($product->getId()->getValue());
    }
}
