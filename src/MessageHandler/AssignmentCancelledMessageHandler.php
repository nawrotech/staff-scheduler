<?php

namespace App\MessageHandler;

use App\Message\AssignmentCancelledMessage;
use App\Repository\ShiftPositionRepository;
use App\Repository\ShiftRepository;
use App\Repository\StaffProfileRepository;
use App\Repository\UserRepository;
use App\Service\NotifierService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


#[AsMessageHandler]
final class AssignmentCancelledMessageHandler
{
    public function __construct(
        private StaffProfileRepository $staffProfileRepository,
        private ShiftRepository $shiftRepository,
        private ShiftPositionRepository $positionRepository,
        private UserRepository $userRepository,
        private NotifierService $notifierService,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public function __invoke(AssignmentCancelledMessage $message): void
    {
        $staffProfile = $this->staffProfileRepository->find($message->getStaffProfileId());
        $shift = $this->shiftRepository->find($message->getShiftId());
        $position = $this->positionRepository->find($message->getPositionId());

        if (!$staffProfile || !$shift || !$position) {
            return;
        }

        $admins = $this->userRepository->findByRole('ROLE_ADMIN');

        foreach ($admins as $admin) {
            $this->notifierService->sendAssignmentCanellationNotification(
                $staffProfile,
                $shift,
                $position,
                $admin->getEmail()
            );
        }
    }
}
