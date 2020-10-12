<?php

namespace App\Model\Shop\Entity\Payment\Method;

class Method
{
    private Id $id;
    private string $title;
    private int $priority;

    public function __construct(Id $id, string $title, int $priority)
    {
        $this->id = $id;
        $this->title = $title;
        $this->priority = $priority;
    }

    public function edit(string $title, int $priority): void
    {
        $this->title = $title;
        $this->priority = $priority;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
