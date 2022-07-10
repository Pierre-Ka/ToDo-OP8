<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const NUMBER_OF_USER = 10;
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < self::NUMBER_OF_USER; $i++) {
            $user = new User();
            if( 0 === $i) {
                $user->setUsername('admin');
                $user->setEmail('admin@admin.fr');
                $user->setRoles(['ROLE_ADMIN']);
            }
            elseif( 1 === $i) {
                $user->setUsername('user');
                $user->setEmail('user@user.fr');
                $user->setRoles(['ROLE_USER']);
            }
            else {
                $name = $faker->name();
                if (strlen($name) > 25) {
                    $name = substr($name, 0, 24);
                }
                $user->setUsername($name);
                $user->setEmail($faker->email());
                $user->setRoles(['ROLE_USER']);
            }
            $password = $this->hasher->hashPassword($user, 'secret');
            $user->setPassword($password);
            $manager->persist($user);
            $this->addReference('user_'.$i, $user);
        }
        $manager->flush();
    }
}

