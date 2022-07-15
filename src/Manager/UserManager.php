<?php

namespace App\Manager;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager implements UserManagerInterface
{
    private UserPasswordHasherInterface $hasher;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $hasher)
    {
        $this->em = $em;
        $this->hasher = $hasher;
    }

    public function new(string $plainPassword, User $user): void
    {
        $password =  $this->hasher->hashPassword($user, $plainPassword);
        $user->setPassword($password);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function update(User $user): void
    {
        $password = $this->hasher->hashPassword($user, $user->getPassword());
        $user->setPassword($password);
        $this->em->flush();
    }
}