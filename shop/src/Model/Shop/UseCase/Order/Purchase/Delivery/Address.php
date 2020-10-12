<?php

namespace App\Model\Shop\UseCase\Order\Purchase\Delivery;

use Symfony\Component\Validator\Constraints as Assert;

class Address
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    public string $city;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    public string $street;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    public string $house;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    public string $apartment;
}
