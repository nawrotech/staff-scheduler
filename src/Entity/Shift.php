<?php

namespace App\Entity;

use App\Repository\ShiftRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShiftRepository::class)]
class Shift
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    private ?\DateTimeImmutable $startTime = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    private ?\DateTimeImmutable $endTime = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    /**
     * @var Collection<int, Assignment>
     */
    #[ORM\OneToMany(targetEntity: Assignment::class, mappedBy: 'shift', orphanRemoval: true)]
    private Collection $assignments;

    /**
     * @var Collection<int, ShiftPosition>
     */
    #[ORM\OneToMany(targetEntity: ShiftPosition::class, mappedBy: 'shift', orphanRemoval: true, cascade: ['persist'])]
    private Collection $shiftPositions;

    public function __construct()
    {
        $this->assignments = new ArrayCollection();
        $this->shiftPositions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getStartTime(): ?\DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeImmutable $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeImmutable
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeImmutable $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * @return Collection<int, Assignment>
     */
    public function getAssignments(): Collection
    {
        return $this->assignments;
    }

    public function addAssignment(Assignment $assignment): static
    {
        if (!$this->assignments->contains($assignment)) {
            $this->assignments->add($assignment);
            $assignment->setShift($this);
        }

        return $this;
    }

    public function removeAssignment(Assignment $assignment): static
    {
        if ($this->assignments->removeElement($assignment)) {
            // set the owning side to null (unless already changed)
            if ($assignment->getShift() === $this) {
                $assignment->setShift(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ShiftPosition>
     */
    public function getShiftPositions(): Collection
    {
        return $this->shiftPositions;
    }

    public function addShiftPosition(ShiftPosition $shiftPosition): static
    {
        if (!$this->shiftPositions->contains($shiftPosition)) {
            $this->shiftPositions->add($shiftPosition);
            $shiftPosition->setShift($this);
        }

        return $this;
    }

    public function removeShiftPosition(ShiftPosition $shiftPosition): static
    {
        if ($this->shiftPositions->removeElement($shiftPosition)) {
            // set the owning side to null (unless already changed)
            if ($shiftPosition->getShift() === $this) {
                $shiftPosition->setShift(null);
            }
        }

        return $this;
    }
}
