<?php

namespace App\Tests\Builder\Shop;

use App\Model\Shop\Entity\Delivery;

class DeliveryMethodBuilder
{
    private Delivery\Method\Method $item;

    public function courier($cost, $minPrice): self
    {
        $this->item = new Delivery\Method\Method(
            Delivery\Method\Id::next(),
            new Delivery\Method\Type(Delivery\Method\Type::COURIER),
            'Курьерская доставка',
            $cost,
            $minPrice
        );

        return $this;
    }

    public function pickup(): self
    {
        $this->item = new Delivery\Method\Method(
            Delivery\Method\Id::next(),
            new Delivery\Method\Type(Delivery\Method\Type::PICKUP),
            'Самовывоз'
        );

        return $this;
    }

    public function build(): Delivery\Method\Method
    {
        return $this->item;
    }
}
