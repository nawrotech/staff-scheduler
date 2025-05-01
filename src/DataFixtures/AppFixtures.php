<?php

namespace App\DataFixtures;

use App\Enum\AssignmentStatus;
use App\Enum\StaffPosition;
use App\Factory\AssignmentFactory;
use App\Factory\ShiftFactory;
use App\Factory\ShiftPositionFactory;
use App\Factory\StaffProfileFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 1. Create a small set of predictable users
        $admin = UserFactory::createOne([
            'email' => 'm@n.com',
            'password' => '123',
            'roles' => ['ROLE_ADMIN', 'ROLE_USER'],
        ]);

        $chef1 = UserFactory::createOne([
            'email' => 'chef1@example.com',
            'password' => '123',
            'roles' => ['ROLE_USER'],
        ]);

        $chef2 = UserFactory::createOne([
            'email' => 'chef2@example.com',
            'password' => '123',
            'roles' => ['ROLE_USER'],
        ]);

        $waiter = UserFactory::createOne([
            'email' => 'waiter@example.com',
            'password' => '123',
            'roles' => ['ROLE_USER'],
        ]);

        // 2. Create staff profiles with clear positions
        $adminProfile = StaffProfileFactory::createOne([
            'name' => 'Admin Manager',
            'user' => $admin,
            'phone' => '123-456-7890',
            'position' => StaffPosition::MANAGER
        ]);

        $chefProfile1 = StaffProfileFactory::createOne([
            'name' => 'Head Chef',
            'user' => $chef1,
            'phone' => '123-456-7891',
            'position' => StaffPosition::CHEF
        ]);

        $chefProfile2 = StaffProfileFactory::createOne([
            'name' => 'Head Chef',
            'user' => $chef2,
            'phone' => '123-456-7891',
            'position' => StaffPosition::CHEF
        ]);

        $waiterProfile = StaffProfileFactory::createOne([
            'name' => 'Senior Waiter',
            'user' => $waiter,
            'phone' => '123-456-7892',
            'position' => StaffPosition::WAITER
        ]);

        // 3. Create a test shift with clearly defined positions
        $testShift = ShiftFactory::createOne([
            'date' => new \DateTimeImmutable('next Monday'),
            'startTime' => new \DateTimeImmutable('10:00'),
            'endTime' => new \DateTimeImmutable('18:00'),
            'notes' => 'Test Shift for Email Notifications'
        ]);

        // 4. Create positions for the shift
        $chefPosition = ShiftPositionFactory::createOne([
            'shift' => $testShift,
            'name' => StaffPosition::CHEF,
            'quantity' => 1
        ]);

        $waiterPosition = ShiftPositionFactory::createOne([
            'shift' => $testShift,
            'name' => StaffPosition::WAITER,
            'quantity' => 1 // Need exactly 1 waiter
        ]);

        // 5. Assign staff to positions
        AssignmentFactory::createOne([
            'shift' => $testShift,
            'shiftPosition' => $chefPosition,
            'staffProfile' => $chefProfile1,
            'status' => AssignmentStatus::PENDING,
            'assignedAt' => new \DateTimeImmutable('now')
        ]);

        AssignmentFactory::createOne([
            'shift' => $testShift,
            'shiftPosition' => $chefPosition,
            'staffProfile' => $chefProfile2,
            'status' => AssignmentStatus::PENDING,
            'assignedAt' => new \DateTimeImmutable('now')
        ]);

        AssignmentFactory::createOne([
            'shift' => $testShift,
            'shiftPosition' => $waiterPosition,
            'staffProfile' => $waiterProfile,
            'status' => AssignmentStatus::PENDING,
            'assignedAt' => new \DateTimeImmutable('now')
        ]);

        // 6. Create a second shift that can be used to test updates
        $updateShift = ShiftFactory::createOne([
            'date' => new \DateTimeImmutable('next Tuesday'),
            'startTime' => new \DateTimeImmutable('09:00'),
            'endTime' => new \DateTimeImmutable('17:00'),
            'notes' => 'Shift to Test Updates'
        ]);

        $updateChefPosition = ShiftPositionFactory::createOne([
            'shift' => $updateShift,
            'name' => StaffPosition::CHEF,
            'quantity' => 1
        ]);

        // Assign the chef1 to this shift too
        AssignmentFactory::createOne([
            'shift' => $updateShift,
            'shiftPosition' => $updateChefPosition,
            'staffProfile' => $chefProfile1,
            'status' => AssignmentStatus::PENDING,
            'assignedAt' => new \DateTimeImmutable('now')
        ]);

        $manager->flush();
    }
}
