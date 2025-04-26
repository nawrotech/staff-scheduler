<?php

namespace App\Entity;

use App\Enum\StaffPosition;
use App\Repository\ShiftRoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShiftRoleRepository::class)]
class ShiftRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'shiftRoles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Shift $shift = null;

    #[ORM\Column(enumType: StaffPosition::class)]
    private ?string $roleName = null;

    #[ORM\Column]
    private ?int $quantity = null;

    /**
     * @var Collection<int, Assignment>
     */
    #[ORM\OneToMany(targetEntity: Assignment::class, mappedBy: 'shiftRole', orphanRemoval: true)]
    private Collection $assignments;

    public function __construct()
    {
        $this->assignments = new ArrayCollection();
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

    public function getRoleName(): ?string
    {
        return $this->roleName;
    }

    public function setRoleName(string $roleName): static
    {
        $this->roleName = $roleName;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

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
            $assignment->setShiftRole($this);
        }

        return $this;
    }

    public function removeAssignment(Assignment $assignment): static
    {
        if ($this->assignments->removeElement($assignment)) {
            // set the owning side to null (unless already changed)
            if ($assignment->getShiftRole() === $this) {
                $assignment->setShiftRole(null);
            }
        }

        return $this;
    }
}
