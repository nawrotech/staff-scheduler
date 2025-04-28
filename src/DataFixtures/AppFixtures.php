<?php

namespace App\DataFixtures;

use App\Enum\StaffPosition;
use App\Factory\StaffProfileFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $admin = UserFactory::createOne([
            'email' => 'm@n.com',
            'password' => '123',
            'roles' => ['ROLE_ADMIN', 'ROLE_USER'],
        ]);

        $adminStaffProfile =  StaffProfileFactory::createOne([
            'name' => 'Master admin',
            'user' => $admin,
            'phone' => '123123123',
            'position' => StaffPosition::MANAGER
        ]);

        $users = UserFactory::createMany(10, function (int $i) {
            return [
                'email' => 'user' . $i . '@example.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
            ];
        });


        foreach ($users as $i => $userProxy) {
            StaffProfileFactory::createOne([
                'name' => 'Staff ' . $i,
                'user' => $userProxy,
                'phone' => '123-456-' . str_pad($i, 4, '0', STR_PAD_LEFT),
            ]);
        }

        $manager->flush();
    }
}
