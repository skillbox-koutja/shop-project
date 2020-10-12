<?php

namespace App\DataFixtures\Shop;

use App\Model\Shop\Entity\Category\Category;
use App\Model\Shop\Entity\Product;
use App\Service\Uploader\FileUploader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixture extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private Product\ProductRepository $products;
    private FileUploader $uploader;

    public function __construct(
        Product\ProductRepository $products,
        FileUploader $uploader
    )
    {
        $this->products = $products;
        $this->uploader = $uploader;
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixture::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $categories = [
            $this->getReference(CategoryFixture::REFERENCE_NEW),
            $this->getReference(CategoryFixture::REFERENCE_SALE),
            $this->getReference(CategoryFixture::REFERENCE_FEMALE),
            $this->getReference(CategoryFixture::REFERENCE_MALE),
            $this->getReference(CategoryFixture::REFERENCE_CHILDREN),
            $this->getReference(CategoryFixture::REFERENCE_ACCESSORIES),
        ];
        $countCategories = count($categories);
        $faker = Factory::create();
        for ($i = 1; $i <= 150; $i++) {
            $product = $this->newProduct(
                $manager,
                $i,
                trim($faker->sentence(random_int(1, 2)), '.'),
                $faker->numberBetween(350, 32000)
            );
            $count = $faker->numberBetween(1, $countCategories);
            foreach ($faker->randomElements($categories, $count) as $category) {
                /** @var Category $category */
                $product->assignCategory($category);
            }
        }
        $manager->flush();
    }

    public function newProduct(
        ObjectManager $manager,
        int $index,
        string $title,
        int $price
    ): Product\Product
    {
        $product = $this->product($title, $price);
        $photo = $this->photo($index, $product);

        $manager->persist($product);
        $manager->persist($photo);

        return $product;
    }

    private function product(string $title, int $price)
    {
        return new Product\Product(
            $this->products->nextId(),
            $title,
            $price
        );
    }

    private function photo(int $index, Product\Product $product): Product\Photo\Photo
    {
        $index = $index % 9;
        if (0 === $index) {
            $index = 9;
        }
        $fileName = __DIR__ . "/products/product-{$index}.jpg";
        $photo = new \SplFileInfo($fileName);
        $photo = $this->uploader->uploadFile($photo);

        return new Product\Photo\Photo(
            $product,
            Product\Photo\Id::next(),
            new Product\Photo\Info(
                $photo->getPath(),
                $photo->getName(),
                $photo->getSize(),
            )
        );
    }

    public static function getGroups(): array
    {
        return [
            'shop-products',
        ];
    }
}
