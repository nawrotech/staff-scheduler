<?php

namespace App\Service;

use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class NotifierService
{
    public function __construct(private NotifierInterface $notifier) {}

    public function sendShiftUpdatedNotification(
        string $staffName,
        array $shiftChanges,
        string $shiftUrl,
        string $staffEmail
    ) {
        $notification = (new Notification('Shift Update: Changes to Your Schedule', ['email']))
            ->content(sprintf(
                "Hello %s,\n\nYour shift has been updated with the following changes:\n\n%s\n\nView Details: %s",
                $staffName,
                implode("\n", $shiftChanges),
                $shiftUrl
            ))
            ->importance(Notification::IMPORTANCE_MEDIUM);

        $this->notifier->send($notification, new Recipient($staffEmail));
    }

    public function sendFulfilledShiftNotification(string $shiftId, string $dateString, string $adminEmail)
    {
        $notification = (new Notification('Shift Fully Staffed', ['email']))
            ->content(sprintf(
                "Good news! The shift '%s' on %s has been fully staffed with all required positions.\n\n" .
                    "All positions have the required number of confirmed staff members assigned.\n\n" .
                    "You can now proceed with final preparations for this shift.",
                $shiftId,
                $dateString
            ))
            ->importance(Notification::IMPORTANCE_MEDIUM);

        $this->notifier->send($notification, new Recipient($adminEmail));
    }
}
