<?php

namespace App\Tests\Unit\Validator;

use App\Entity\Assignment;
use App\Entity\ShiftPosition;
use App\Enum\AssignmentStatus;
use App\Enum\StaffPosition;
use App\Repository\AssignmentRepository;
use App\Validator\AssignmentApprovalLimit;
use App\Validator\AssignmentApprovalLimitValidator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class AssignmentApprovalLimitValidatorTest extends ConstraintValidatorTestCase
{
    private MockObject|AssignmentRepository $assignmentRepository;
    private MockObject|EntityManagerInterface $entityManager;
    private MockObject|UnitOfWork $unitOfWork;

    protected function createValidator(): ConstraintValidatorInterface
    {
        $this->assignmentRepository = $this->createMock(AssignmentRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->unitOfWork = $this->createMock(UnitOfWork::class);

        $this->entityManager->method('getUnitOfWork')->willReturn($this->unitOfWork);

        assert($this->assignmentRepository instanceof AssignmentRepository);
        assert($this->entityManager instanceof EntityManagerInterface);


        return new AssignmentApprovalLimitValidator($this->assignmentRepository, $this->entityManager);
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new AssignmentApprovalLimit());
        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid(): void
    {
        $this->validator->validate('', new AssignmentApprovalLimit());
        $this->assertNoViolation();
    }

    public function testInvalidConstraintType(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(new Assignment(), new class extends Constraint {}); // Anonymous constraint
    }

    public function testInvalidValueType(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate(new \stdClass(), new AssignmentApprovalLimit());
    }

    public function testAssignmentWithoutShiftPositionIsValid(): void
    {
        $assignment = new Assignment();
        $assignment->setStatus(AssignmentStatus::APPROVED);

        $this->unitOfWork->method('getOriginalEntityData')
            ->with($assignment)
            ->willReturn(['status' => AssignmentStatus::PENDING]);

        $this->validator->validate($assignment, new AssignmentApprovalLimit());
        $this->assertNoViolation();
    }

    /**
     * @dataProvider provideValidScenarios
     */
    public function testValidScenarios(
        Assignment $assignment,
        ?AssignmentStatus $originalStatus,
        int $currentApprovedCount,
        int $positionLimit
    ): void {
        $shiftPosition = $assignment->getShiftPosition();
        if ($shiftPosition) {
            $shiftPosition->setQuantity($positionLimit);

            $this->assignmentRepository->expects($this->any())
                ->method('countApprovedByShiftPosition')
                ->with($shiftPosition)
                ->willReturn($currentApprovedCount);
        }

        $this->unitOfWork->method('getOriginalEntityData')
            ->with($assignment)
            ->willReturn(['status' => $originalStatus]);

        $this->validator->validate($assignment, new AssignmentApprovalLimit());
        $this->assertNoViolation();
    }

    public function provideValidScenarios(): \Generator
    {
        // Scenario 1: Status not changing to APPROVED
        yield 'status not approved' => [
            $this->createAssignment(AssignmentStatus::PENDING), // New status
            AssignmentStatus::REJECTED, // Original status
            1, // Current approved
            2, // Limit
        ];

        // Scenario 2: Status changing FROM APPROVED
        yield 'status changing from approved' => [
            $this->createAssignment(AssignmentStatus::REJECTED), // New status
            AssignmentStatus::APPROVED, // Original status
            2, // Current approved (includes this one)
            2, // Limit
        ];

        // Scenario 3: Status changing TO APPROVED, limit NOT reached
        yield 'status changing to approved, limit not reached' => [
            $this->createAssignment(AssignmentStatus::APPROVED), // New status
            AssignmentStatus::PENDING, // Original status
            0, // Current approved (doesn't include this one yet)
            2, // Limit
        ];

        // Scenario 4: Status changing TO APPROVED, limit IS reached, but assignment was ALREADY approved
        yield 'status changing to approved, limit reached, already approved' => [
            $this->createAssignment(AssignmentStatus::APPROVED), // New status
            AssignmentStatus::APPROVED, // Original status (no actual change)
            2, // Current approved (includes this one)
            2, // Limit
        ];

        // Scenario 5: Status changing TO APPROVED, limit NOT reached (exactly 1 below limit)
        yield 'status changing to approved, limit almost reached' => [
            $this->createAssignment(AssignmentStatus::APPROVED), // New status
            AssignmentStatus::PENDING, // Original status
            1, // Current approved
            2, // Limit
        ];
    }

    /**
     * @dataProvider provideInvalidScenarios
     */
    public function testInvalidScenarios(
        Assignment $assignment,
        ?AssignmentStatus $originalStatus,
        int $currentApprovedCount,
        int $positionLimit
    ): void {
        $shiftPosition = $assignment->getShiftPosition();
        $shiftPosition->setQuantity($positionLimit); // Set limit

        // Mock repository count
        $this->assignmentRepository->expects($this->once())
            ->method('countApprovedByShiftPosition')
            ->with($shiftPosition)
            ->willReturn($currentApprovedCount);

        // Mock UoW original data
        $this->unitOfWork->method('getOriginalEntityData')
            ->with($assignment)
            ->willReturn(['status' => $originalStatus]);

        $constraint = new AssignmentApprovalLimit();
        $this->validator->validate($assignment, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ limit }}', (string) $positionLimit)
            ->setParameter('{{ positionName }}', $shiftPosition->getName()?->value ?? 'N/A')
            ->setParameter('{{ currentApproved }}', (string) $currentApprovedCount)
            ->assertRaised();
    }

    public function provideInvalidScenarios(): \Generator
    {
        // Scenario 1: Changing to APPROVED, limit reached exactly, was not approved before
        yield 'limit reached exactly' => [
            $this->createAssignment(AssignmentStatus::APPROVED), // New status
            AssignmentStatus::PENDING, // Original status
            2, // Current approved count (limit will be hit)
            2, // Limit
        ];

        // Scenario 2: Changing to APPROVED, limit exceeded, was not approved before
        yield 'limit exceeded' => [
            $this->createAssignment(AssignmentStatus::APPROVED), // New status
            AssignmentStatus::REJECTED, // Original status
            3, // Current approved count (already over limit)
            2, // Limit
        ];
    }

    /**
     * Helper method to create an Assignment with a ShiftPosition.
     */
    private function createAssignment(
        ?AssignmentStatus $status,
        string $positionName = 'Test Position',
    ): Assignment {
        $shiftPosition = new ShiftPosition();
        $shiftPosition->setName(StaffPosition::tryFrom($positionName) ?? StaffPosition::WAITER);

        $assignment = new Assignment();
        $assignment->setShiftPosition($shiftPosition);
        $assignment->setStatus($status);

        return $assignment;
    }
}
