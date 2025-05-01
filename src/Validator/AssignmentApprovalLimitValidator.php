<?php

namespace App\Validator;

use App\Entity\Assignment;
use App\Enum\AssignmentStatus;
use App\Repository\AssignmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class AssignmentApprovalLimitValidator extends ConstraintValidator
{

    public function __construct(
        private readonly AssignmentRepository $assignmentRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function validate(mixed $value, Constraint $constraint): void
    {

        if (!$constraint instanceof AssignmentApprovalLimit) {
            throw new UnexpectedTypeException($constraint, AssignmentApprovalLimit::class);
        }


        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof Assignment) {
            // If the value is null or not an Assignment, ignore (other constraints handle nullability)
            if (null === $value || '' === $value) {
                return;
            }
            throw new UnexpectedValueException($value, Assignment::class);
        }

        $assignment = $value;
        $newStatus = $assignment->getStatus();

        $unitOfWork = $this->entityManager->getUnitOfWork();
        $originalData = $unitOfWork->getOriginalEntityData($assignment);
        $originalStatus = $originalData['status'] ?? null;

        if ($newStatus !== AssignmentStatus::APPROVED || $newStatus === $originalStatus) {
            return;
        }

        $shiftPosition = $assignment->getShiftPosition();

        if (!$shiftPosition) {
            return;
        }

        $limit = $shiftPosition->getQuantity();

        $currentApprovedCount = $this->assignmentRepository->countApprovedByShiftPosition($shiftPosition);

        $isAlreadyApproved = ($originalStatus === AssignmentStatus::APPROVED);


        if (!$isAlreadyApproved && $currentApprovedCount >= $limit) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ limit }}', (string) $limit)
                ->setParameter('{{ positionName }}', $shiftPosition->getName()?->value ?? 'N/A')
                ->setParameter('{{ currentApproved }}', (string) $currentApprovedCount)
                ->addViolation();
        }
    }
}
