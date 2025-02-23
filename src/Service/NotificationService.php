<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\Ticket;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Psr\Log\LoggerInterface;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailerInterface $mailer,
        private Environment $twig,
        private UrlGeneratorInterface $urlGenerator,
        private LoggerInterface $logger
    ) {}

    public function notifyNewTicket(Ticket $ticket): void
    {
        try {
            $utilisateur = $ticket->getUtilisateur();
            
            // Créer la notification in-app
            $notification = new Notification();
            $notification->setTitre('Nouveau ticket créé');
            $notification->setMessage("Votre ticket \"{$ticket->getTitre()}\" a été créé avec succès.");
            $notification->setType(Notification::TYPE_NOUVEAU_TICKET);
            $notification->setUtilisateur($utilisateur);
            $notification->setTicket($ticket);

            $this->entityManager->persist($notification);
            $this->entityManager->flush();

            // Envoyer l'email
            $this->sendEmail(
                $utilisateur->getEmail(),
                'Nouveau ticket créé',
                'emails/nouveau_ticket.html.twig',
                [
                    'ticket' => $ticket,
                    'utilisateur' => $utilisateur
                ]
            );
        } catch (\Exception $e) {
            $this->logger->error('Error in notifyNewTicket: ' . $e->getMessage(), [
                'exception' => $e,
                'ticket_id' => $ticket->getId(),
                'user_id' => $utilisateur->getId()
            ]);
        }
    }

    public function notifyStatusChange(Ticket $ticket, string $oldStatus, string $newStatus): void
    {
        $this->logger->info('Début de notifyStatusChange', [
            'ticket_id' => $ticket->getId(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus
        ]);

        // N'envoyer des notifications que pour les statuts "résolu" et "clôturé"
        if ($newStatus !== 'résolu' && $newStatus !== 'clôturé') {
            $this->logger->info('Pas de notification envoyée car le nouveau statut n\'est ni résolu ni clôturé', [
                'new_status' => $newStatus
            ]);
            return;
        }

        $utilisateur = $ticket->getUtilisateur();
        if (!$utilisateur) {
            $this->logger->warning('Pas d\'utilisateur associé au ticket', [
                'ticket_id' => $ticket->getId()
            ]);
            return;
        }

        $this->logger->info('Préparation de l\'envoi de notification à l\'utilisateur', [
            'user_id' => $utilisateur->getId(),
            'user_email' => $utilisateur->getEmail()
        ]);

        $subject = $newStatus === 'résolu' 
            ? 'Votre ticket a été résolu'
            : 'Votre ticket a été clôturé';

        try {
            $this->sendEmail(
                $utilisateur->getEmail(),
                $subject,
                'emails/statut_change.html.twig',
                [
                    'ticket' => $ticket,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ]
            );

            $this->logger->info('Notification de changement de statut envoyée avec succès', [
                'ticket_id' => $ticket->getId(),
                'user_email' => $utilisateur->getEmail(),
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'envoi de la notification de changement de statut', [
                'error' => $e->getMessage(),
                'ticket_id' => $ticket->getId(),
                'user_email' => $utilisateur->getEmail()
            ]);
            throw $e;
        }
    }

    public function notifyTicketResolved(Ticket $ticket): void
    {
        try {
            $utilisateur = $ticket->getUtilisateur();
            
            // Créer la notification in-app
            $notification = new Notification();
            $notification->setTitre('Ticket résolu');
            $notification->setMessage("Votre ticket \"{$ticket->getTitre()}\" a été résolu.");
            $notification->setType(Notification::TYPE_TICKET_RESOLU);
            $notification->setUtilisateur($utilisateur);
            $notification->setTicket($ticket);

            $this->entityManager->persist($notification);
            $this->entityManager->flush();

            // Envoyer l'email
            $this->sendEmail(
                $utilisateur->getEmail(),
                'Ticket résolu',
                'emails/ticket_resolu.html.twig',
                [
                    'ticket' => $ticket,
                    'utilisateur' => $utilisateur
                ]
            );
        } catch (\Exception $e) {
            $this->logger->error('Error in notifyTicketResolved: ' . $e->getMessage(), [
                'exception' => $e,
                'ticket_id' => $ticket->getId(),
                'user_id' => $utilisateur->getId()
            ]);
        }
    }

    private function sendEmail(string $to, string $subject, string $template, array $context): void
    {
        try {
            $this->logger->info('Début de l\'envoi d\'email', [
                'to' => $to,
                'subject' => $subject,
                'template' => $template
            ]);

            $html = $this->twig->render($template, $context);
            
            $this->logger->info('Template rendu avec succès', [
                'template' => $template,
                'context' => array_keys($context)
            ]);

            $email = (new Email())
                ->from('ame.sanglant2004@gmail.com')
                ->to($to)
                ->subject($subject)
                ->html($html);

            $this->logger->info('Email configuré, tentative d\'envoi', [
                'from' => 'ame.sanglant2004@gmail.com',
                'to' => $to,
                'subject' => $subject
            ]);

            $this->mailer->send($email);
            
            $this->logger->info('Email envoyé avec succès', [
                'to' => $to,
                'subject' => $subject,
                'template' => $template
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'envoi de l\'email: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'to' => $to,
                'subject' => $subject,
                'template' => $template
            ]);
            throw $e;
        }
    }
}
