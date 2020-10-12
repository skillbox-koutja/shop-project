<?php

namespace App\Model\Shop\UseCase\Order\Purchase;


use App\Model\Flusher;
use App\Model\Shop\Entity\Delivery;
use App\Model\Shop\Entity\Order;
use App\Model\Shop\Entity\Payment;
use App\Model\Shop\Entity\Product;

class Handler
{
    private Order\OrderRepository $orderRepository;
    private Product\ProductRepository $productRepository;
    private Delivery\Method\MethodRepository $deliveryMethodRepository;
    private Payment\Method\MethodRepository $paymentMethodRepository;
    private Flusher $flusher;

    public function __construct(
        Order\OrderRepository $orderRepository,
        Product\ProductRepository $productRepository,
        Delivery\Method\MethodRepository $deliveryMethodRepository,
        Payment\Method\MethodRepository $paymentMethodRepository,
        Flusher $flusher
    )
    {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->deliveryMethodRepository = $deliveryMethodRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $deliveryMethod = $this->deliveryMethodRepository->findByType(
            $deliveryMethodType = new Delivery\Method\Type($command->deliveryMethodType)
        );
        $paymentMethod = $this->paymentMethodRepository->get(
            new Payment\Method\Id($command->paymentMethodId)
        );
        $product = $this->productRepository->get(
            new Product\Id($command->productId)
        );
        $order = new Order\Order(
            $this->orderRepository->nextId(),
            $this->customer($deliveryMethodType, $command),
            $paymentMethod,
            $deliveryMethod,
            [$product],
            new \DateTimeImmutable(),
            $command->note
        );
        $this->orderRepository->add($order);
        $this->flusher->flush($order);
    }

    public function customer(
        Delivery\Method\Type $deliveryMethodType,
        Command $command
    ): Order\Customer\Data
    {
        $customer = new Order\Customer\Data(
            $command->email,
            $command->phone,
            implode(' ', array_filter([
                $command->lastName,
                $command->firstName,
                $command->patronymic ?? ''
            ])),
        );
        if ($deliveryMethodType->isCourier()) {
            $customer->addAddress(
                $command->city,
                $command->street,
                $command->house,
                $command->apartment
            );
        }

        return $customer;
    }
}
