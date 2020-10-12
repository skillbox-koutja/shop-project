<?php

namespace App\Tests\Unit\Model\Shop\Entity\Category;

use App\Tests\Builder\Shop\CategoryBuilder;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    public function test()
    {
        $category = (new CategoryBuilder())->build('test', 'test');
        $this->assertFalse($category->isNew());
        $this->assertFalse($category->isSale());
    }

    public function testCreateNew()
    {
        $category = (new CategoryBuilder())->new()->build();
        $this->assertTrue($category->isNew());
        $this->assertFalse($category->isSale());
    }

    public function testCreateSale()
    {
        $category = (new CategoryBuilder())->sale()->build();
        $this->assertFalse($category->isNew());
        $this->assertTrue($category->isSale());
    }
}
