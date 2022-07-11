<?php

namespace App\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    private UserPasswordHasherInterface $hasher;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $hasher)
    {
        $this->em = $em;
        $this->hasher = $hasher;
    }

    // Pas de form dans le manager => traitement seulement ($password, $user)
    public function new($form, $user): void
    {
        $plainPassword = $form->get('password')->getData();
        $password =  $this->hasher->hashPassword($user, $plainPassword);
        $user->setPassword($password);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function update($user): void
    {
        $password = $this->hasher->hashPassword($user, $user->getPassword());
        $user->setPassword($password);
        $this->em->flush();
    }
}