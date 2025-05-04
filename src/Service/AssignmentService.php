<?php

namespace App\Service;

use App\Entity\Assignment;
use App\Message\AssignmentCancelledMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class AssignmentService
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface $bus
    ) {}

    public function cancelAssignment(Assignment $assignment, bool $wasApproved): void
    {
        $staffProfileId = $assignment->getStaffProfile()->getId();
        $shiftId = $assignment->getShift()->getId();
        $positionId = $assignment->getShiftPosition()->getId();

        $this->em->remove($assignment);
        $this->em->flush();

        if ($wasApproved) {
            $this->bus->dispatch(new AssignmentCancelledMessage(
                $staffProfileId,
                $shiftId,
                $positionId
            ));
        }
    }
}
