<?php


namespace App\DataFixtures\Shop;

use App\Model\Shop\Entity\Delivery;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class DeliveryMethodFixture extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->courier());
        $manager->persist($this->pickup());
        $manager->flush();
    }

    private function courier()
    {
        return new Delivery\Method\Method(
            Delivery\Method\Id::next(),
            new Delivery\Method\Type(Delivery\Method\Type::COURIER),
            'Курьерская доставка',
            280,
            2000
        );
    }

    private function pickup()
    {
        return new Delivery\Method\Method(
            Delivery\Method\Id::next(),
            new Delivery\Method\Type(Delivery\Method\Type::PICKUP),
            'Самовывоз'
        );
    }

    public static function getGroups(): array
    {
        return [
            'shop-delivery-methods',
        ];
    }
}
