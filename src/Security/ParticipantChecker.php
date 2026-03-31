<?php

namespace App\Security;

use App\Entity\Participant;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ParticipantChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Participant){
            return;
        }
        if (!$user->isActif()){
            throw new AccountExpiredException('Votre compte est inactif. Contactez l\'administrateur.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
