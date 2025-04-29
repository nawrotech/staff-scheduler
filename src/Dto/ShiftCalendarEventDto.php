<?php

namespace App\Dto;

final class ShiftCalendarEventDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $start,
        public readonly string $end,
        public readonly array $extendedProps,
    ) {}

    public static function fromShift(\App\Entity\Shift $shift): self
    {
        $date = $shift->getDate()->format('Y-m-d');
        $startDateTime = new \DateTimeImmutable($date . ' ' . $shift->getStartTime()->format('H:i:s'));
        $endDateTime   = new \DateTimeImmutable($date . ' ' . $shift->getEndTime()->format('H:i:s'));

        return new self(
            $shift->getId(),
            'Shift',
            $startDateTime->format('Y-m-d\TH:i:s'),
            $endDateTime->format('Y-m-d\TH:i:s'),
            ['notes' => $shift->getNotes()]
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'start' => $this->start,
            'end' => $this->end,
            'extendedProps' => $this->extendedProps,
        ];
    }
}
