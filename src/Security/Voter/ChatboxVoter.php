<?php

namespace App\Security\Voter;

use App\Entity\Chatbox;
use App\Entity\Personne;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ChatboxVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Chatbox && in_array($attribute, ['view', 'send_message']);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof Personne) {
            return false;
        }

        /** @var Chatbox $chatbox */
        $chatbox = $subject;

        return match($attribute) {
            'view' => $this->canView($chatbox, $user),
            'send_message' => $this->canSendMessage($chatbox, $user),
            default => false
        };
    }

    private function canView(Chatbox $chatbox, Personne $user): bool
    {
        // L'utilisateur peut voir la chatbox s'il est lié au ticket
        $ticket = $chatbox->getTicket();
        if (!$ticket) {
            return false;
        }

        // Si l'utilisateur est un admin, il peut voir toutes les chatbox
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // L'utilisateur peut voir la chatbox s'il est l'auteur du ticket
        if ($ticket->getUtilisateur() === $user) {
            return true;
        }

        // Le technicien assigné peut voir la chatbox
        if ($ticket->getTechnicien() === $user) {
            return true;
        }

        return false;
    }

    private function canSendMessage(Chatbox $chatbox, Personne $user): bool
    {
        // Pour envoyer un message, il faut d'abord pouvoir voir la chatbox
        if (!$this->canView($chatbox, $user)) {
            return false;
        }

        // Vérifier si le ticket n'est pas fermé
        $ticket = $chatbox->getTicket();
        return $ticket && $ticket->getStatut() !== 'CLOSED';
    }
}
