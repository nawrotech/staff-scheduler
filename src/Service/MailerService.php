<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerService
{
    public function __construct(private MailerInterface $mailer) {}


    public function sendShiftUpdatedEmail(
        string $recipientEmail,
        string $recipientName,
        array $changes,
        string $shiftUrl,
        \DateTimeInterface $shiftDate
    ): void {
        $email = (new TemplatedEmail())
            ->to(new Address($recipientEmail))
            ->subject(sprintf('Shift Updated for %s – Please Review', $shiftDate->format('M j, Y')))
            ->htmlTemplate('emails/shift_updated.html.twig')
            ->textTemplate('emails/shift_updated.txt.twig')
            ->context([
                'recipient_name' => $recipientName,
                'changes' => $changes,
                'shift_url' => $shiftUrl,
                'shift_date' => $shiftDate,
            ]);

        $this->mailer->send($email);
    }
}
