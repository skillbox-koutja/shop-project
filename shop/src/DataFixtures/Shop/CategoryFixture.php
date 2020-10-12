<?php

namespace App\DataFixtures\Shop;

use App\Model\Shop\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixture extends Fixture
{
    public const REFERENCE_NEW = 1;
    public const REFERENCE_SALE = 2;
    public const REFERENCE_FEMALE = 3;
    public const REFERENCE_MALE = 4;
    public const REFERENCE_CHILDREN = 5;
    public const REFERENCE_ACCESSORIES = 6;

    public function load(ObjectManager $manager)
    {
        $manager->persist($new = $this->create(
            'Новинка',
            Category\Type::NEW
        ));
        $this->setReference(self::REFERENCE_NEW, $new);

        $manager->persist($sale = $this->create(
            'Распродажа',
            Category\Type::SALE
        ));
        $this->setReference(self::REFERENCE_SALE, $sale);

        $manager->persist($female = $this->create(
            'Женщины',
            'female'
        ));
        $this->setReference(self::REFERENCE_FEMALE, $female);

        $manager->persist($male = $this->create(
            'Мужчины',
            'male'
        ));
        $this->setReference(self::REFERENCE_MALE, $male);

        $manager->persist($children = $this->create(
            'Дети',
            'children'
        ));
        $this->setReference(self::REFERENCE_CHILDREN, $children);

        $manager->persist($accessories = $this->create(
            'Аксессуары',
            'accessories'
        ));
        $this->setReference(self::REFERENCE_ACCESSORIES, $accessories);

        $manager->flush();
    }

    private function create(string $title, string $slug)
    {
        return new Category\Category(
            Category\Id::next(),
            mb_strtolower($slug),
            $title
        );
    }
}
