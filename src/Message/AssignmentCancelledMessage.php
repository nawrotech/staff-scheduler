<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final class AssignmentCancelledMessage
{
    public function __construct(
        private int $staffProfileId,
        private int $shiftId,
        private int $positionId
    ) {}

    public function getStaffProfileId(): int
    {
        return $this->staffProfileId;
    }

    public function getShiftId(): int
    {
        return $this->shiftId;
    }

    public function getPositionId(): int
    {
        return $this->positionId;
    }
}
