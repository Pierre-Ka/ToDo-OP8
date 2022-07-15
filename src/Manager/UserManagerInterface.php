<?php

namespace App\Manager;

use App\Entity\User;

interface UserManagerInterface
{
    public function new(string $plainPassword, User $user);

    public function update(User $user);
}