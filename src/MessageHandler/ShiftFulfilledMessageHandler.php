<?php

namespace App\MessageHandler;

use App\Message\ShiftFulfilledMessage;
use App\Repository\ShiftRepository;
use App\Repository\UserRepository;
use App\Service\NotifierService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ShiftFulfilledMessageHandler
{

    public function __construct(
        private UserRepository $userRepository,
        private NotifierService $notifierService,
        private ShiftRepository $shiftRepository
    ) {}

    public function __invoke(ShiftFulfilledMessage $message): void
    {
        $admins = $this->userRepository->findByRole('ROLE_ADMIN');

        $shift = $this->shiftRepository->find($message->getShiftId());

        foreach ($admins as $admin) {
            $this->notifierService->sendFulfilledShiftNotification(
                $shift->getId(),
                $shift->getDate()->format('Y-m-d'),
                $admin->getEmail()
            );
        }
    }
}
