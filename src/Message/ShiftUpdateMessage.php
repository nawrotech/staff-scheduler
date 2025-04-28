<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final class ShiftUpdatedMessage
{
    public function __construct(
        private int   $shiftId,
        private array $changeSet
    ) {}

    public function getShiftId(): int
    {
        return $this->shiftId;
    }

    public function getChangeSet(): array
    {
        return $this->changeSet;
    }
}
