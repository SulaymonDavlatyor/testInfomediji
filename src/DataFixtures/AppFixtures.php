<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $users = [];

        // Create 20 users
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail("user$i@example.com");
            $user->setPassword('$2y$13$EFz7VRsWIHABhT4jJvAhEuPQiHLy.UVLqGT0PlMolY4fWqShQXYjy');
            $manager->persist($user);
            $users[] = $user;
        }

        // Subscribe first 18 users to the last 2 users
        for ($i = 0; $i < 18; $i++) {
            foreach (array_slice($users, -2) as $subscribedUser) {
                $subscription = new Subscription();
                $subscription->setSubscriberId($users[$i]);
                $subscription->setSubscribedToId($subscribedUser);
                $manager->persist($subscription);
            }
        }

        $manager->flush();
    }
}
