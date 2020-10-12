<?php

namespace App\Tests\Unit\Model\Shop\Entity\Product;

use App\Tests\Builder\Shop\CategoryBuilder;
use App\Tests\Builder\Shop\ProductBuilder;
use PHPUnit\Framework\TestCase;

class UpdateCategoriesTest extends TestCase
{
    public function testSuccess(): void
    {
        $categoryBuilder = new CategoryBuilder();
        $init = [
            $new = $categoryBuilder->new()->build(),
            $sale = $categoryBuilder->sale()->build(),
            $test = $categoryBuilder->build('test', 'test'),
            $test2 = $categoryBuilder->build('test2', 'test2'),
            $test3 = $categoryBuilder->build('test3', 'test3'),
        ];
        $update = [
            $test,
            $test3,
        ];
        $removed = [
            $new,
            $sale,
            $test2,
        ];

        $product = (new ProductBuilder())
            ->withCategories($init)
            ->build();
        $this->assertTrue($product->isNew());
        $this->assertTrue($product->isSale());
        $this->assertCount(3, $product->getCategories());

        $product->updateCategories($update);
        $this->assertFalse($product->isNew());
        $this->assertFalse($product->isSale());
        foreach ($product->getCategories() as $category) {
            $this->assertContains($category, $update);
        }
        foreach ($product->getCategories() as $category) {
            $this->assertNotContains($category, $removed);
        }
    }
}
