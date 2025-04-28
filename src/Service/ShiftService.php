<?php

namespace App\Service;

use App\Entity\Shift;
use App\Message\ShiftUpdatedMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ShiftService
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface  $bus,
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
}
