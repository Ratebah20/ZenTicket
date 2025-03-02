<?php

namespace App\Controller;

use App\Entity\Technicien;
use App\Entity\Ticket;
use App\Form\TechnicienType;
use App\Repository\TechnicienRepository;
use App\Repository\TicketRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/technicien')]
#[IsGranted('ROLE_TECHNICIEN')]
class TechnicienController extends AbstractController
{
    #[Route('/', name: 'technicien_dashboard')]
    public function dashboard(TicketRepository $ticketRepository): Response
    {
        /** @var Technicien $technicien */
        $technicien = $this->getUser();
        $assignedTickets = $ticketRepository->findByTechnician($technicien->getId());
        $newTickets = $ticketRepository->createQueryBuilder('t')
            ->leftJoin('t.categorie', 'c')
            ->andWhere('t.technicien IS NULL')
            ->andWhere('t.statut = :statut')
            ->setParameter('statut', 'nouveau')
            ->getQuery()
            ->getResult();

        return $this->render('technicien/dashboard.html.twig', [
            'assignedTickets' => $assignedTickets,
            'newTickets' => $newTickets,
        ]);
    }

    #[Route('/tickets', name: 'technicien_tickets_list')]
    public function listTickets(TicketRepository $ticketRepository): Response
    {
        /** @var Technicien $technicien */
        $technicien = $this->getUser();
        $tickets = $ticketRepository->findByTechnician($technicien->getId());

        return $this->render('technicien/dashboard.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/ticket/{id}/take', name: 'technicien_take_ticket', methods: ['POST'])]
    public function takeTicket(Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        /** @var Technicien $technicien */
        $technicien = $this->getUser();
        
        try {
            $technicien->prendreEnCharge($ticket);
            $entityManager->flush();
            $this->addFlash('success', 'Ticket pris en charge avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Impossible de prendre en charge ce ticket.');
        }

        return $this->redirectToRoute('technicien_dashboard');
    }

    #[Route('/ticket/{id}/close', name: 'technicien_close_ticket', methods: ['POST'])]
    public function closeTicket(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        /** @var Technicien $technicien */
        $technicien = $this->getUser();
        $solution = $request->request->get('solution');

        try {
            $technicien->cloturerTicket($ticket, $solution);
            $entityManager->flush();
            $this->addFlash('success', 'Ticket clôturé avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('technicien_dashboard');
    }

    #[Route('/ticket/{id}/status', name: 'technicien_update_status', methods: ['POST'])]
    public function updateStatus(
        Request $request, 
        Ticket $ticket, 
        EntityManagerInterface $entityManager,
        NotificationService $notificationService,
        LoggerInterface $logger
    ): Response {
        /** @var Technicien $technicien */
        $technicien = $this->getUser();
        $newStatus = $request->request->get('status');
        $oldStatus = $ticket->getStatut();

        $logger->info('Début de updateStatus', [
            'ticket_id' => $ticket->getId(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'technicien_id' => $technicien->getId()
        ]);

        try {
            $logger->info('Modification du statut du ticket', [
                'ticket_id' => $ticket->getId(),
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

            $technicien->modifierStatut($ticket, $newStatus);
            $entityManager->flush();

            $logger->info('Statut du ticket mis à jour, envoi de la notification', [
                'ticket_id' => $ticket->getId(),
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

            // Envoyer la notification de changement de statut
            $notificationService->notifyStatusChange($ticket, $oldStatus, $newStatus);

            $logger->info('Notification envoyée avec succès');
            $this->addFlash('success', 'Statut du ticket mis à jour.');
        } catch (\Exception $e) {
            $logger->error('Erreur lors de la mise à jour du statut', [
                'error' => $e->getMessage(),
                'ticket_id' => $ticket->getId(),
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);
            $this->addFlash('error', 'Impossible de modifier le statut du ticket : ' . $e->getMessage());
        }

        return $this->redirectToRoute('technicien_dashboard');
    }

    #[Route('/ticket/{id}/solution', name: 'technicien_add_solution', methods: ['POST'])]
    public function addSolution(
        Request $request, 
        Ticket $ticket, 
        EntityManagerInterface $entityManager, 
        NotificationService $notificationService,
        LoggerInterface $logger
    ): Response {
        /** @var Technicien $technicien */
        $technicien = $this->getUser();
        $solution = $request->request->get('solution');
        $markAsResolved = $request->request->has('markAsResolved');
        $oldStatus = $ticket->getStatut();

        $logger->info('Début de addSolution', [
            'ticket_id' => $ticket->getId(),
            'technicien_id' => $technicien->getId(),
            'mark_as_resolved' => $markAsResolved
        ]);

        try {
            // Ajout de la solution
            $technicien->ajouterSolution($ticket, $solution);
            
            // Si l'option est cochée, marquer comme résolu
            if ($markAsResolved) {
                $technicien->modifierStatut($ticket, Ticket::STATUT_RESOLU);
                
                // Notifier l'utilisateur du changement de statut
                $notificationService->notifyTicketResolved($ticket);
                $logger->info('Notification envoyée à l\'utilisateur pour la résolution du ticket', [
                    'ticket_id' => $ticket->getId(),
                    'user_id' => $ticket->getUtilisateur()->getId()
                ]);
            }
            
            $entityManager->flush();
            
            if ($markAsResolved) {
                $this->addFlash('success', 'Solution ajoutée et ticket marqué comme résolu avec succès.');
            } else {
                $this->addFlash('success', 'Solution ajoutée avec succès.');
            }
        } catch (\Exception $e) {
            $logger->error('Erreur lors de l\'ajout de solution', [
                'error' => $e->getMessage(),
                'ticket_id' => $ticket->getId()
            ]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('technicien_dashboard');
    }

    #[Route('/profile', name: 'technicien_profile')]
    public function profile(): Response
    {
        // Redirection vers le profil général
        return $this->redirectToRoute('app_profile_index');
    }
}
