<?php

namespace App\Entity;

use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends RoleVoter
{
    protected function extractRoles(TokenInterface $token)
    {
        $user = $token->getUser();

        // si le user existe vraiment, on utilise la méthode getRole, sinon on retourne un tableau vide
        return $user instanceof UserInterface ?  $user->getRoles() : [];
    }
}