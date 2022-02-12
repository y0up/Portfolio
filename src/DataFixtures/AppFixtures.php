<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne([
            'email' => 'admin@example.com',
            'roles' => ['ROLE_ADMIN'],
            'password' => '$2y$13$NiJhPE9Nu1F34fE/dKyI2.ThXjChoh6KWUc9R4drgoYjPzkvmp8gG',
    ]);

        $manager->flush();
    }
}
