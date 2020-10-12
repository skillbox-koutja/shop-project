<?php

declare(strict_types=1);

namespace App\Model;

interface AggregateRoot
{
    /**
     * @return EventInterface[]
     */
    public function releaseEvents(): array;
}
