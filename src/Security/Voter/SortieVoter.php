<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class SortieVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    public const INS = 'INS';
    public const DESINS = 'DESINS';
    public const PUBLISH = 'PUBLISH';
    public const CANCEL = 'CANCEL';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::INS, self::DESINS, self::PUBLISH, self::CANCEL, self::DELETE])
            && $subject instanceof \App\Entity\Sortie;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        $now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            $vote?->addReason('Vous devez être connecté');
            return false;
        }
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }


        $sortie = $subject;

        switch ($attribute) {
            case self::EDIT:
                if ($sortie->getOrganisateur() === $user &&
                    $sortie->getEtat()->getNom() == "En creation"
                ) {
                    return true;
                }
                return false;


            case self::VIEW:
                // vérifie si la sortie est en création => ne s'affiche pas! sauf si organisateur
                if ($sortie->getEtat()->getNom() === 'En creation' &&
                    $sortie->getOrganisateur() !== $user
                ) {
                    return false;
                }
                return true;

            case self::INS:
                // vérifie les conditions pour s'inscrire à une sortie (date limite, nb place et pas déjà inscrit ok)

                if ($sortie->getEtat()->getNom() === 'Ouverte' &&
                    $sortie->getDateLimiteInscription() > $now &&
                    $sortie->getInscriptions()->count() < $sortie->getNbPlaces() &&
                    !$sortie->getInscriptions()->contains($user)
                ) {
                    return true;
                }
                return false;

            case self::DESINS:
                // vérifie les conditions pour se désinscrire à une sortie
                if (($sortie->getEtat()->getNom() === 'Ouverte' || $sortie->getEtat()->getNom() === 'Cloturee') &&
                    $sortie->getDateLimiteInscription() > $now &&
                    $user !== $sortie->getOrganisateur() &&
                    $sortie->getInscriptions()->contains($user)
                ) {
                    return true;
                }
                return false;


            case self::PUBLISH:
                // vérifie les conditions pour publier une sortie
                if ($sortie->getEtat()->getNom() == "En creation" &&
                    $sortie->getOrganisateur() === $user
                ) {
                    return true;
                }
                return false;


            case self::CANCEL:
                // vérifie les conditions pour annuler une sortie
                if ($sortie->getEtat()->getNom() == "Ouverte" &&
                    $sortie->getOrganisateur() === $user
                ) {
                    return true;
                }
                return false;

            case self::DELETE:
                // vérifie les conditions pour supprimer une sortie
                if ($sortie->getEtat()->getNom() == "En creation" &&
                    $sortie->getOrganisateur() === $user
                ) {
                    return true;
                }
                return false;

        }

        return false;
    }
}
