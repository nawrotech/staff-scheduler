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

        $summary = $this->formatChanges($message->getChangeSet());

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
                $summary['changes'],
                $shiftUrl,
                $summary['originalDate'] ?? $shift->getDate()
            );
        }
    }

    private function formatChanges(array $changeSet): array
    {
        $summary = [];
        $originalDate = null;

        foreach ($changeSet as $field => [$old, $new]) {
            if ($field === 'date' && $old instanceof \DateTimeInterface) {
                $originalDate = $old;
            }

            $summary[] = $this->createChangeSummary($field, $old, $new);
        }

        return [
            'changes' => $summary,
            'originalDate' => $originalDate
        ];
    }

    private function createChangeSummary(string $field, $old, $new): string
    {
        return sprintf(
            '%s: %s → %s',
            ucfirst($field),
            $this->formatValue($old),
            $this->formatValue($new)
        );
    }

    private function formatValue(mixed $value): string
    {
        if (!$value instanceof \DateTimeInterface) {
            return (string)$value;
        }

        $isTimeOnly = $value->format('Y-m-d') === '1970-01-01';
        $isDateOnly = $value->format('H:i:s') === '00:00:00';

        if ($isTimeOnly) {
            return $value->format('H:i');
        }

        if ($isDateOnly) {
            return $value->format('Y-m-d');
        }

        return $value->format('Y-m-d H:i');
    }
}
