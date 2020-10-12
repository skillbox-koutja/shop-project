<?php

declare(strict_types=1);

namespace App\Model\Shop\Entity\Order;

use App\Model\AggregateRoot;
use App\Model\EventsTrait;
use App\Model\Shop\Entity\Delivery;
use App\Model\Shop\Entity\Payment;
use App\Model\Shop\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Webmozart\Assert\Assert;

class Order implements AggregateRoot
{
    use EventsTrait;

    private Id $id;
    private Status $status;
    private int $progress;
    private Customer\Data $customer;
    private Payment\Method\Method $paymentMethod;
    private Delivery\Method\Method $deliveryMethod;
    /** @var Product\Product[]|ArrayCollection */
    private $products;
    private \DateTimeImmutable $created;
    private int $cost;
    private ?string $note;
    private int $version;

    public function __construct(
        Id $id,
        Customer\Data $customer,
        Payment\Method\Method $paymentMethod,
        Delivery\Method\Method $deliveryMethod,
        array $products,
        \DateTimeImmutable $created,
        ?string $note = null
    )
    {
        Assert::notEmpty($products);
        $this->id = $id;
        $this->status = Status::undone();
        $this->progress = 0;
        $this->customer = $customer;
        $this->paymentMethod = $paymentMethod;
        $this->deliveryMethod = $deliveryMethod;
        $this->created = $created;
        $this->note = $note;
        $this->products = new ArrayCollection($products);
        $this->cost = $this->calculateCost();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getCreated(): \DateTimeImmutable
    {
        return $this->created;
    }

    public function undone(): void
    {
        $this->changeStatus(Status::undone());
    }

    public function done(): void
    {
        $this->changeStatus(Status::done());
    }

    public function isUndone(): bool
    {
        return $this->status->isUndone();
    }

    public function isDone(): bool
    {
        return $this->status->isDone();
    }

    private function calculateCost(): int
    {
        if ($this->products->isEmpty()) {
            throw new \DomainException('Products is empty');
        }
        $cost = array_reduce(
            $this->products->toArray(),
            function ($cost, Product\Product $product) {
                $cost += $product->getPrice();
                return $cost;
            },
            0);
        $deliveryMethod = $this->deliveryMethod;
        if ($deliveryMethod->getType()->isCourier()) {
            if ($cost < $deliveryMethod->getMinPrice()) {
                $cost += $deliveryMethod->getCost();
            }
        }
        if ($cost < 0) {
            throw new \DomainException('Cost less the zero');
        }

        return $cost;
    }

    private function changeProgress(int $progress): void
    {
        Assert::range($progress, 0, 100);
        if ($progress === $this->progress) {
            throw new \DomainException('Progress is already same.');
        }
        $this->progress = $progress;
    }

    public function changeStatus(Status $status): void
    {
        if ($this->status->isEqual($status)) {
            throw new \DomainException('Status is already same.');
        }
        $this->status = $status;
        if ($status->isDone()) {
            if ($this->progress !== 100) {
                $this->changeProgress(100);
            }
        }
        if ($status->isUndone()) {
            if ($this->progress !== 0) {
                $this->changeProgress(0);
            }
        }
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getCustomer(): Customer\Data
    {
        return $this->customer;
    }

    public function getPaymentMethod(): Payment\Method\Method
    {
        return $this->paymentMethod;
    }

    public function getDeliveryMethod(): Delivery\Method\Method
    {
        return $this->deliveryMethod;
    }

    /**
     * @return Product\Product[]
     */
    public function getProducts(): array
    {
        return $this->products->toArray();
    }

    public function getCost(): int
    {
        return $this->cost;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

}
