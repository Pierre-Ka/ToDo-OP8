<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TaskFixture extends Fixture implements DependentFixtureInterface
{
    public const NUMBER_OF_TASK = 100;

    public function load(ObjectManager $manager): void  {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < self::NUMBER_OF_TASK; $i++) {
            $task = new Task();
            $task->setCreatedAt($faker->dateTimeThisYear());
            $task->setTitle($faker->word());
            $task->setContent($faker->sentence(6, true));
            $referentTime = $faker->dateTimeBetween('-6 months', 'now');
            $userKey = rand(0, UserFixtures::NUMBER_OF_USER - 1);
            /** @var User $user */
            $user = $this->getReference('user_' . $userKey);
            if ( $task->getCreatedAt() >= $referentTime ) {
                $task->setUser($user);
            }
            if($task->getCreatedAt() >= $faker->dateTimeThisMonth()) {
                $task->toggle(false);
            }
            else {
                $task->toggle(true);
            }
            $manager->persist($task);
        }
        $manager->flush();
    }

    public function getDependencies(): array  {

        return [
            UserFixtures::class,
        ];
    }
}
