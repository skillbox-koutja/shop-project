<?php

namespace App\Tests\Builder\Shop;

use App\Model\Shop\Entity\Payment;

class PaymentMethodBuilder
{
    public function build($title, $priority): Payment\Method\Method
    {
        return new Payment\Method\Method(
            Payment\Method\Id::next(),
            $title,
            $priority
        );
    }
}
