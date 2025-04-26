<?php

namespace App\Entity;

use App\Repository\AvailabilityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity(fields: ['staffProfile', 'weekStart'])]
#[ORM\Entity(repositoryClass: AvailabilityRepository::class)]
class Availability
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'availabilities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?StaffProfile $staffProfile = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $weekStart = null;

    #[ORM\Column]
    private array $availableSlots = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStaffProfile(): ?StaffProfile
    {
        return $this->staffProfile;
    }

    public function setStaffProfile(?StaffProfile $staffProfile): static
    {
        $this->staffProfile = $staffProfile;

        return $this;
    }

    public function getWeekStart(): ?\DateTimeImmutable
    {
        return $this->weekStart;
    }

    public function setWeekStart(\DateTimeImmutable $weekStart): static
    {
        $this->weekStart = $weekStart;

        return $this;
    }

    public function getAvailableSlots(): array
    {
        return $this->availableSlots;
    }

    public function setAvailableSlots(array $availableSlots): static
    {
        $this->availableSlots = $availableSlots;

        return $this;
    }
}
