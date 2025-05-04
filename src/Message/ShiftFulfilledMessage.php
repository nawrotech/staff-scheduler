<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final class ShiftFulfilledMessage
{

    public function __construct(
        private int $shiftId,
    ) {}

    public function getShiftId(): int
    {
        return $this->shiftId;
    }
}
