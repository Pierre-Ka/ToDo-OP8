<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    public function supports(string $attribute, mixed $subject): bool
    {
        if ('delete' !== $attribute) {
            return false;
        }
        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $userConnected = $token->getUser();
        if (!$userConnected instanceof User) {
            return false;
        }
        /** @var Task $task */
        $task = $subject;

        return $userConnected === $task->getUser()  ||
            (null === $task->getUser() && in_array('ROLE_ADMIN', $userConnected->getRoles()));
    }
}
