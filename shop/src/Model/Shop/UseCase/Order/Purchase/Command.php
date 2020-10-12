<?php

namespace App\Model\Shop\UseCase\Order\Purchase;

use App\Model\Shop\Entity\Delivery;
use App\Model\Shop\Entity\Product\Product;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    public string $lastName;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    public string $firstName;
    /**
     * @Assert\Length(max="255")
     */
    public string $patronymic;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    public string $phone;
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public string $email;
    /**
     * @Assert\NotBlank()
     */
    public string $deliveryMethodType;
    /**
     * @Assert\NotBlank()
     */
    public string $paymentMethodId;
    /**
     * @Assert\Length(max="500")
     */
    public string $note;
    /**
     * @Assert\NotBlank()
     */
    public int $id;
    /**
     * @Assert\NotBlank()
     */
    public int $productId;
    /**
     * @Assert\NotBlank(groups={"delivery_method_courier"})
     * @Assert\Length(max="255")
     */
    public string $city;
    /**
     * @Assert\NotBlank(groups={"delivery_method_courier"})
     * @Assert\Length(max="255")
     */
    public string $street;
    /**
     * @Assert\NotBlank(groups={"delivery_method_courier"})
     * @Assert\Length(max="255")
     */
    public string $house;
    /**
     * @Assert\NotBlank(groups={"delivery_method_courier"})
     * @Assert\Length(max="255")
     */
    public string $apartment;

    public function __construct(int $productId)
    {
        $this->productId = $productId;
    }

    public static function fromProduct(
        Product $product,
        Delivery\Method\Type $deliveryMethodType,
        string $paymentMethodId
    ): self
    {
        $command = new self($product->getId()->getValue());
        $command->deliveryMethodType = (string) $deliveryMethodType;
        $command->paymentMethodId = $paymentMethodId;

        return $command;
    }
}
