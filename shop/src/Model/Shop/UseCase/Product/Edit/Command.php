<?php

declare(strict_types=1);

namespace App\Model\Shop\UseCase\Product\Edit;

use App\Model\Shop\Entity\Category\Category;
use App\Model\Shop\Entity\Product\Product;
use App\Model\Shop\UseCase\Product\PhotoInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public int $id;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    public string $title;
    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThan(value="0")
     */
    public int $price;

    public bool $sale = false;

    public bool $new = false;

    /**
     * @var PhotoInfo|UploadedFile
     */
    public $photo;

    /**
     * @var Category[]
     * @Assert\NotBlank()
     */
    public array $categories;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromProduct(Product $product)
    {
        $command = new self($product->getId()->getValue());

        $command->categories = array_map(function (Category $category) {
            return $category->getSlug();
        }, $product->getCategories());
        $command->sale = $product->isSale();
        $command->new = $product->isNew();
        $command->price = $product->getPrice();
        $command->title = $product->getTitle();

        return $command;

    }
}
