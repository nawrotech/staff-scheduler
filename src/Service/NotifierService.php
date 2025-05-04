<?php

namespace App\Service;

use App\Entity\Shift;
use App\Entity\ShiftPosition;
use App\Entity\StaffProfile;
use App\Notification\AssignmentCancellationNotification;
use App\Notification\ShiftUpdatedNotification;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NotifierService
{
    public function __construct(
        private NotifierInterface $notifier,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public function sendShiftUpdatedNotification(
        string $staffName,
        array $shiftChanges,
        string $shiftUrl,
        string $staffEmail
    ) {
        $notification = (new ShiftUpdatedNotification($staffName, $shiftChanges, $shiftUrl));

        $this->notifier->send($notification, new Recipient($staffEmail));
    }


    public function sendFulfilledShiftNotification(
        string $shiftId,
        string $dateString,
        string $adminEmail
    ) {
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

    public function sendAssignmentCanellationNotification(
        StaffProfile $staffProfile,
        Shift $shift,
        ShiftPosition $position,
        string $adminEmail
    ): void {
        $shiftUrl = $this->urlGenerator->generate('admin_shift_manage', [
            'id' => $shift->getId()
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $notification = new AssignmentCancellationNotification(
            $staffProfile,
            $shift,
            $position,
            $shiftUrl
        );

        $this->notifier->send($notification, new Recipient($adminEmail));
    }
}
