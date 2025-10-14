<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->isBlocked()) {
            throw new CustomUserMessageAuthenticationException(
                'Votre compte a été bloqué par un administrateur. Vous pouvez contacter l\'administrateur du site à cette adresse pour plus d\'informations : corentin.polard@gmail.com'
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Ici tu peux aussi bloquer les utilisateurs inactifs ou non vérifiés si besoin
    }
}
