<?php

namespace App\Service;

use App\Entity\Shift;
use App\Enum\AssignmentStatus;
use App\Message\ShiftFulfilledMessage;
use App\Message\ShiftUpdatedMessage;
use App\Repository\AssignmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ShiftService
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface  $bus,
        private AssignmentRepository $assignmentRepository
    ) {}

    public function save(Shift $shift): void
    {
        $uow = $this->em->getUnitOfWork();
        $uow->computeChangeSets();
        $changeSet = $uow->getEntityChangeSet($shift);

        $this->em->persist($shift);
        $this->em->flush();

        if (!empty($changeSet)) {
            $this->bus->dispatch(new ShiftUpdatedMessage(
                $shift->getId(),
                $changeSet
            ));
        }
    }

    public function checkAndDispatchShiftFulfilled(Shift $shift): void
    {
        $positionCounts = $this->assignmentRepository
            ->getPositionCountsForShift($shift, AssignmentStatus::PENDING);

        $isFilled = true;

        foreach ($shift->getShiftPositions() as $position) {
            $positionId = $position->getId();
            $requiredCount = $position->getQuantity();
            $actualCount = $positionCounts[$positionId] ?? 0;

            if ($actualCount < $requiredCount) {
                $isFilled = false;
            }
        }

        if ($isFilled) {
            $this->bus->dispatch(new ShiftFulfilledMessage($shift->getId()));
        }
    }
}
