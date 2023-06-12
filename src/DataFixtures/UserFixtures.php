<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('test@test.fr')
            ->setPassword('$2y$13$duZgvLpAoBhgcnjNKhM2Zul4qww8nTLQsYwJAGuK7Pz6F0EdF2YFS')
            ->setRegisteryDate(new DateTime('now'));

        $manager->persist($user);
        $manager->flush();
    }
}
