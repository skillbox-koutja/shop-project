<?php

namespace App\Model\Shop\UseCase\Order\Status;

use App\Model\Flusher;
use App\Model\Shop\Entity\Order;

class Handler
{
    private Order\OrderRepository $orderRepository;
    private Flusher $flusher;

    public function __construct(
        Order\OrderRepository $orderRepository,
        Flusher $flusher
    )
    {
        $this->orderRepository = $orderRepository;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $order = $this->orderRepository->get(new Order\Id($command->id));

        $order->changeStatus(new Order\Status($command->status));

        $this->flusher->flush($order);
    }
}
