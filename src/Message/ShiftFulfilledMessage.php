<?php

namespace App\Message;

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
