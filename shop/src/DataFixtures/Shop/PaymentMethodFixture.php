<?php


namespace App\DataFixtures\Shop;

use App\Model\Shop\Entity\Payment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class PaymentMethodFixture extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->method('Банковской картой', 100));
        $manager->persist($this->method('Наличные', 200));
        $manager->flush();
    }

    private function method($title, $priority)
    {
        return new Payment\Method\Method(
            Payment\Method\Id::next(),
            $title,
            $priority
        );
    }

    public static function getGroups(): array
    {
        return [
            'shop-payment-methods',
        ];
    }
}
