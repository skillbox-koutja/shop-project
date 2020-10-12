<?php

declare(strict_types=1);

namespace App\Model;

trait EventsTrait
{
    /** @var EventInterface[] */
    private array $recordedEvents = [];

    protected function recordEvent(EventInterface $event): void
    {
        $this->recordedEvents[] = $event;
    }

    public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];
        return $events;
    }
}
