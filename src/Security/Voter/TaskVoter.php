<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    protected function supports(string $attribute, $object): bool
    {
        if ('delete' !== $attribute) {
            return false;
        }
        if (!$object instanceof Task) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $object, TokenInterface $token): bool
    {
        $userConnected = $token->getUser();
        if (!$userConnected instanceof User) {
            return false;
        }
        /** @var Task $task */
        $task = $object;

        return $userConnected === $task->getUser()  ||
            (null === $task->getUser() && in_array('ROLE_ADMIN', $userConnected->getRoles()));
    }
}
