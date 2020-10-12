<?php

namespace App\Model\Shop\UseCase\Order\Status;

use App\Model\Shop\Entity\Delivery;
use App\Model\Shop\Entity\Order\Order;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public int $id;
    /**
     * @Assert\NotBlank()
     */
    public string $status;

    public function __construct(int $id, string $status)
    {
        $this->id = $id;
        $this->status = $status;
    }

    public static function fromOrder(
        Order $order
    ): self
    {
        return new self(
            $order->getId()->getValue(),
            (string) $order->getStatus()->getName()
        );
    }
}
