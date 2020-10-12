<?php

declare(strict_types=1);

namespace App\Model\Shop\Entity\Order\Customer;

class Data
{
    private string $email;
    private string $phone;
    private string $name;
    private ?string $city;
    private ?string $street;
    private ?string $house;
    private ?string $apartment;

    public function __construct(
        string $email,
        string $phone,
        string $name
    )
    {
        $this->email = $email;
        $this->phone = $phone;
        $this->name = $name;
    }

    public function addAddress(
        string $city,
        string $street,
        string $house,
        string $apartment
    ): void
    {
        $this->city = $city;
        $this->street = $street;
        $this->house = $house;
        $this->apartment = $apartment;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getHouse(): ?string
    {
        return $this->house;
    }

    public function getApartment(): ?string
    {
        return $this->apartment;
    }
}
