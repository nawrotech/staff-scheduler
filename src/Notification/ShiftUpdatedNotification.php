<?php

namespace App\Notification;

use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

class ShiftUpdatedNotification extends Notification implements EmailNotificationInterface
{
    public function __construct(
        private string $staffName,
        private array $shiftChanges,
        private string $shiftUrl
    ) {
        parent::__construct('Shift Update: Changes to Your Schedule');

        $this->content(sprintf(
            "Hello %s,\n\nYour shift has been updated with the following changes:\n\n%s\n\n" .
                "View Details: [Click here to view shift details](%s)",
            $staffName,
            implode("\n", $shiftChanges),
            $shiftUrl
        ))->importance(Notification::IMPORTANCE_MEDIUM)
        ;
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $email = (new NotificationEmail())
            ->to($recipient->getEmail())
            ->subject($this->getSubject())
            ->markdown(sprintf(
                "Hello %s,\n\nYour shift has been updated with the following changes:\n\n%s",
                $this->staffName,
                implode("\n- ", $this->shiftChanges)
            ))
            ->action('View Shift Details', $this->shiftUrl)
            ->importance($this->getImportance());

        return new EmailMessage($email);
    }
}
