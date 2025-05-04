<?php

namespace App\Notification;

use App\Entity\ShiftPosition;
use App\Entity\StaffProfile;
use App\Entity\Shift;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

class AssignmentCancellationNotification extends Notification implements EmailNotificationInterface
{
    public function __construct(
        private StaffProfile $staffProfile,
        private Shift $shift,
        private ShiftPosition $position,
        private string $shiftUrl
    ) {
        parent::__construct('Urgent: Staff Member Canceled Approved Assignment');

        $this->content(sprintf(
            "Staff member %s has canceled their approved assignment for %s on %s from %s to %s.\n\n" .
                "Position: %s\n\n" .
                "You may need to find a replacement.",
            $this->staffProfile->getName(),
            'Shift #' . $this->shift->getId(),
            $this->shift->getDate()->format('Y-m-d'),
            $this->shift->getStartTime()?->format('H:i') ?? 'N/A',
            $this->shift->getEndTime()?->format('H:i') ?? 'N/A',
            $this->position->getName()->value
        ))->importance(Notification::IMPORTANCE_HIGH);
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $email = (new NotificationEmail())
            ->to($recipient->getEmail())
            ->subject($this->getSubject())
            ->importance($this->getImportance())
            ->markdown(sprintf(
                "## Shift Assignment Cancellation Alert\n\n" .
                    "Staff member **%s** has canceled their approved assignment for a shift:\n\n" .
                    "- **Date:** %s\n" .
                    "- **Time:** %s - %s\n" .
                    "- **Position:** %s\n\n" .
                    "This position now requires a replacement staff member.",
                $this->staffProfile->getName(),
                $this->shift->getDate()->format('Y-m-d'),
                $this->shift->getStartTime()?->format('H:i') ?? 'N/A',
                $this->shift->getEndTime()?->format('H:i') ?? 'N/A',
                $this->position->getName()->value
            ))
            ->action('View Shift Details', $this->shiftUrl);

        return new EmailMessage($email);
    }
}
