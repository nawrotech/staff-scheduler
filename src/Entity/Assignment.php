<?php

namespace App\Entity;

use App\Enum\AssignmentStatus;
use App\Repository\AssignmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity(fields: ['shift', 'staffProfile'])]
#[ORM\Entity(repositoryClass: AssignmentRepository::class)]
class Assignment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'assignments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Shift $shift = null;

    #[ORM\ManyToOne(inversedBy: 'assignments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?StaffProfile $staffProfile = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $assignedAt = null;

    #[ORM\ManyToOne(inversedBy: 'assignments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ShiftPosition $shiftPosition = null;

    #[ORM\Column(type: 'string', enumType: AssignmentStatus::class)]
    private ?AssignmentStatus $status = null;


    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShift(): ?Shift
    {
        return $this->shift;
    }

    public function setShift(?Shift $shift): static
    {
        $this->shift = $shift;

        return $this;
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

    public function getAssignedAt(): ?\DateTimeImmutable
    {
        return $this->assignedAt;
    }

    public function setAssignedAt(\DateTimeImmutable $assignedAt): static
    {
        $this->assignedAt = $assignedAt;

        return $this;
    }

    public function getShiftPosition(): ?ShiftPosition
    {
        return $this->shiftPosition;
    }

    public function setShiftPosition(?ShiftPosition $shiftPosition): static
    {
        $this->shiftPosition = $shiftPosition;

        return $this;
    }

    public function getStatus(): ?AssignmentStatus
    {
        return $this->status;
    }

    public function setStatus(AssignmentStatus $status): static
    {
        $this->status = $status;

        return $this;
    }


}
