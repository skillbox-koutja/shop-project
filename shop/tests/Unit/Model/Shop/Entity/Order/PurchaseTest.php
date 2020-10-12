<?php

namespace App\Tests\Unit\Model\Shop\Entity\Order;

use App\Model\Shop\Entity\Order;
use App\Tests\Builder\Shop\DeliveryMethodBuilder;
use App\Tests\Builder\Shop\PaymentMethodBuilder;
use App\Tests\Builder\Shop\ProductBuilder;
use PHPUnit\Framework\TestCase;

class PurchaseTest extends TestCase
{
    public function testCostCourierLessMinPriceSuccess(): void
    {
        $minPrice = 2000;
        $price = $minPrice;
        $costDelivery = 280;
        $cost = $minPrice;

        $this->orderCourier($cost, $price, $costDelivery, $minPrice);
    }

    public function testCostCourierMoreMinPriceSuccess(): void
    {
        $minPrice = 2000;
        $price = 1900;
        $costDelivery = 280;
        $cost = $price + $costDelivery;

        $this->orderCourier($cost, $price, $costDelivery, $minPrice);
    }

    private function orderCourier($cost, $price, $costDelivery, $minPrice)
    {
        $product = (new ProductBuilder())->withPrice($price)->build();
        $deliveryMethod = (new DeliveryMethodBuilder())->courier($costDelivery, $minPrice)->build();
        $paymentMethod = (new PaymentMethodBuilder())->build('test', 1);

        $order = new Order\Order(
            $id = new Order\Id(1),
            $customer = new Order\Customer\Data('email@test.app', '89997772211', 'Brad Pitt'),
            $paymentMethod,
            $deliveryMethod,
            [$product],
            $created = new \DateTimeImmutable('2020-10-25T00:00:00'),
            $note = 'test'
        );

        $this->assertEquals($id, $order->getId());
        $this->assertEquals($created, $order->getCreated());
        $this->assertEquals($cost, $order->getCost());
        $this->assertEquals($note, $order->getNote());
        $this->assertEquals($paymentMethod, $order->getPaymentMethod());
        $this->assertEquals($deliveryMethod, $order->getDeliveryMethod());
        $this->assertEquals($customer, $order->getCustomer());
        $this->assertTrue($order->isUndone());
        $this->assertFalse($order->isDone());

        $order->done();
        $this->assertTrue($order->isDone());
        $this->assertFalse($order->isUndone());

        $order->undone();
        $this->assertTrue($order->isUndone());
        $this->assertFalse($order->isDone());
    }
}
