<?php

namespace App\MessageHandler;

use App\Enum\AssignmentStatus;
use App\Message\ShiftUpdatedMessage;
use App\Repository\AssignmentRepository;
use App\Repository\ShiftRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsMessageHandler]
class ShiftUpdatedHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private ShiftRepository $shifts,
        private UrlGeneratorInterface $router,
        private AssignmentRepository $assignmentRepository,
        private MailerService $mailerService
    ) {}

    public function __invoke(ShiftUpdatedMessage $message): void
    {
        $shift = $this->shifts->find($message->getShiftId());
        if (!$shift) return;

        $approved = $this->assignmentRepository
            ->findBy([
                'shift'  => $shift,
                'status' => AssignmentStatus::APPROVED
            ]);

        if (empty($approved)) {
            return;
        }

        $summary = [];
        foreach ($message->getChangeSet() as $field => [$old, $new]) {
            if ($old instanceof \DateTimeInterface) {
                $oldStr = $field === 'date'
                    ? $old->format('Y-m-d')
                    : $old->format('H:i');
            } else {
                $oldStr = (string)$old;
            }

            if ($new instanceof \DateTimeInterface) {
                $newStr = $field === 'date'
                    ? $new->format('Y-m-d')
                    : $new->format('H:i');
            } else {
                $newStr = (string)$new;
            }

            $summary[] = sprintf(
                '%s: %s → %s',
                ucfirst($field),
                $oldStr,
                $newStr
            );
        }

        $shiftUrl = $this->router->generate(
            'shift_show',
            ['id' => $shift->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        foreach ($approved as $assignment) {
            $user = $assignment->getStaffProfile()->getUser();
            $staffName = $assignment->getStaffProfile()->getName();

            $this->mailerService->sendShiftUpdatedEmail(
                $user->getEmail(),
                $staffName,
                $summary,
                $shiftUrl,
                $shift->getDate() // TO DO OLD DATE SHOULD BE DISPLAYED IF EXISTS
            );
        }
    }
}
