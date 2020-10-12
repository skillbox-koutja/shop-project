<?php

namespace App\Tests\Unit\Model\Shop\Entity\Product;

use App\Model\Shop\Entity\Product\Id;
use App\Model\Shop\Entity\Product\Product;
use App\Tests\Builder\Shop\CategoryBuilder;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    public function testSuccess(): void
    {
        $categoryBuilder = new CategoryBuilder();
        $categories = [
            $new = $categoryBuilder->new()->build(),
            $sale = $categoryBuilder->sale()->build(),
            $test = $categoryBuilder->build('test', 'test'),
        ];

        $product = new Product(
            $id = new Id(1),
            $title = 'product',
            $price = 100
        );
        $product->updateCategories($categories);

        $this->assertEquals($id, $product->getId());
        $this->assertEquals($title, $product->getTitle());
        $this->assertEquals($price, $product->getPrice());
        $this->assertTrue($product->isNew());
        $this->assertTrue($product->isSale());
        $this->assertCount(1, $product->getCategories());
        foreach ($product->getCategories() as $category) {
            $this->assertContains($category, [$test]);
        }
        foreach ($product->getCategories() as $category) {
            $this->assertNotContains($category, [$new, $sale]);
        }
    }
}
