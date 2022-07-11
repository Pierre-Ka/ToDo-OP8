<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

// Garanti que la suppression n'est effectué que par le propriétaire de la tache ou admin si anonyme
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
        if('delete' === $attribute) {
                return $this->canDelete($task, $userConnected);
        }
        throw new \LogicException('This code should not be reached!');
    }

    private function canDelete(Task $task, User $userConnected): bool
    {
        // Remplacer le $userConnected->getRoles()[0] ?=> in_array()
         return $userConnected === $task->getUser()  ||
             (null === $task->getUser() && 'ROLE_ADMIN' === $userConnected->getRoles()[0]);
    }
}
