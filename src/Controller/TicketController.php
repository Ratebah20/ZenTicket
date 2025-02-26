<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\Chatbox;
use App\Entity\Utilisateur;
use App\Entity\Technicien;
use App\Entity\Commentaire;
use App\Form\TicketType;
use App\Form\CommentaireType;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Service\NotificationService;

#[Route('/ticket')]
class TicketController extends AbstractController
{
    #[Route("/new", name: "app_ticket_new", methods: ["GET", "POST"])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, NotificationService $notificationService, LoggerInterface $logger): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour créer un ticket.');
            return $this->redirectToRoute('app_login');
        }

        $ticket = new Ticket();
        $ticket->setUtilisateur($this->getUser());  // Définir l'utilisateur dès la création
        $ticket->setStatut(Ticket::STATUT_NOUVEAU);
        $ticket->setDateCreation(new \DateTime());

        $form = $this->createForm(TicketType::class, $ticket);
        
        try {
            $form->handleRequest($request);
            
            $logger->info('Formulaire de ticket soumis', [
                'isSubmitted' => $form->isSubmitted(),
                'isValid' => $form->isSubmitted() ? $form->isValid() : false,
                'method' => $request->getMethod(),
                'data' => $request->request->all(),
                'user' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'anonymous'
            ]);

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    // Créer une nouvelle chatbox pour le ticket
                    $chatbox = new Chatbox();
                    $chatbox->setTicket($ticket);
                    $ticket->setChatbox($chatbox);
                    
                    // Gérer le commentaire initial s'il existe
                    $commentaireInitial = $form->get('commentaireInitial')->getData();
                    if ($commentaireInitial) {
                        $commentaire = new Commentaire();
                        $commentaire->setContenu($commentaireInitial);
                        $commentaire->setDateCreation(new \DateTime());
                        $commentaire->setTicket($ticket);
                        $commentaire->setAuteur($this->getUser());
                        $entityManager->persist($commentaire);
                    }
                    
                    // Sauvegarder le ticket
                    $entityManager->persist($chatbox);
                    $entityManager->persist($ticket);
                    $entityManager->flush();

                    $logger->info('Ticket créé avec succès', [
                        'ticket_id' => $ticket->getId(),
                        'user' => $this->getUser()->getUserIdentifier(),
                        'titre' => $ticket->getTitre()
                    ]);

                    // Envoyer la notification
                    try {
                        $notificationService->notifyNewTicket($ticket);
                        $logger->info('Notification envoyée avec succès');
                    } catch (\Exception $e) {
                        $logger->error('Erreur lors de l\'envoi de la notification', [
                            'error' => $e->getMessage()
                        ]);
                    }

                    $this->addFlash('success', 'Le ticket a été créé avec succès.');
                    return $this->redirectToRoute('chat_view', ['id' => $chatbox->getId()]);
                } else {
                    $logger->warning('Formulaire invalide', [
                        'errors' => $this->getFormErrors($form)
                    ]);
                    
                    foreach ($this->getFormErrors($form) as $error) {
                        $this->addFlash('error', $error);
                    }
                }
            }
        } catch (\Exception $e) {
            $logger->error('Erreur lors de la création du ticket', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'anonymous'
            ]);
            
            $this->addFlash('error', 'Une erreur est survenue lors de la création du ticket : ' . $e->getMessage());
        }

        return $this->render('ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    private function getFormErrors($form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }
        return $errors;
    }

    #[Route("/", name: "app_ticket_index", methods: ["GET"])]
    #[IsGranted('ROLE_USER')]
    public function index(TicketRepository $ticketRepository): Response
    {
        $user = $this->getUser();
        
        if ($this->isGranted('ROLE_TECHNICIEN')) {
            $tickets_ouverts = $ticketRepository->findOpenTickets();
            $tickets_fermes = $ticketRepository->findClosedTickets();
        } else {
            $tickets_ouverts = $ticketRepository->findOpenTicketsByUser($user);
            $tickets_fermes = $ticketRepository->findClosedTicketsByUser($user);
        }

        return $this->render('ticket/index.html.twig', [
            'tickets_ouverts' => $tickets_ouverts,
            'tickets_fermes' => $tickets_fermes,
        ]);
    }

    #[Route("/{id}/edit", name: "app_ticket_edit", methods: ["GET", "POST"])]
    #[IsGranted('ROLE_TECHNICIEN')]
    public function edit(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_index');
        }

        return $this->render('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/{id}", name: "app_ticket_show", methods: ["GET", "POST"])]
    public function show(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        // Création du formulaire de commentaire
        $commentaire = new Commentaire();
        $commentForm = $this->createForm(CommentaireType::class, $commentaire, [
            'action' => $this->generateUrl('app_commentaire_add', ['id' => $ticket->getId()])
        ]);

        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    #[Route("/{id}/delete", name: "app_ticket_delete", methods: ["POST"])]
    public function delete(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $entityManager->remove($ticket);
            $entityManager->flush();

            $this->addFlash('success', 'Le ticket a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_profile_tickets');
    }

    #[Route("/{id}/assign-utilisateur/{utilisateurId}", name: "app_ticket_assign_utilisateur", methods: ["POST"])]
    public function assignUtilisateur(Request $request, Ticket $ticket, EntityManagerInterface $entityManager, Utilisateur $utilisateur): Response
    {
        // Vérifier si l'utilisateur est autorisé
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Seul un administrateur peut réassigner un ticket.');
        }

        if (!$ticket) {
            throw $this->createNotFoundException('Ticket not found.');
        }

        try {
            $ticket->setUtilisateur($utilisateur);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a été assigné avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de l\'assignation de l\'utilisateur.');
        }

        return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
    }

    #[Route("/{id}/assign/{technicien}", name: "app_ticket_assign_technician", methods: ["POST"])]
    public function assignTechnician(Request $request, Ticket $ticket, EntityManagerInterface $entityManager, Technicien $technicien): Response
    {
        // Vérifier si l'utilisateur est autorisé
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPERVISEUR')) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à assigner un technicien.');
        }

        if (!$ticket) {
            throw $this->createNotFoundException('Ticket not found.');
        }

        try {
            $ticket->setTechnicien($technicien);
            $ticket->setStatut(Ticket::STATUT_EN_COURS);
            $entityManager->flush();

            $this->addFlash('success', 'Le technicien a été assigné avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de l\'assignation du technicien.');
        }

        return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
    }

    #[Route("/{id}/validate-solution", name: "app_ticket_validate_solution", methods: ["POST"])]
    public function validateSolution(Request $request, Ticket $ticket, EntityManagerInterface $entityManager, NotificationService $notificationService): Response
    {
        // Vérifier si l'utilisateur est le créateur du ticket
        if ($ticket->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à valider cette solution.');
        }

        if ($this->isCsrfTokenValid('validate'.$ticket->getId(), $request->request->get('_token'))) {
            try {
                if ($ticket->getDateResolution() === null) {
                    throw new \InvalidArgumentException('Le ticket doit être résolu avant d\'être clôturé.');
                }

                $ticket->setStatut(Ticket::STATUT_CLOTURE);
                $ticket->setDateCloture(new \DateTime());
                $ticket->setSolutionValidee(true);
                $entityManager->flush();

                // Envoyer la notification
                $notificationService->notifyTicketResolved($ticket);

                $this->addFlash('success', 'Le ticket a été clôturé avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la clôture du ticket: ' . $e->getMessage());
            }
        }

        return $this->redirectToRoute('app_profile_tickets');
    }

    #[Route("/{id}/resolve", name: "app_ticket_resolve", methods: ["POST"])]
    public function resolve(Request $request, Ticket $ticket, EntityManagerInterface $entityManager, NotificationService $notificationService): Response
    {
        // Vérifier si l'utilisateur est autorisé (doit être un technicien)
        if (!$this->isGranted('ROLE_TECHNICIEN')) {
            throw $this->createAccessDeniedException('Seul un technicien peut résoudre un ticket.');
        }

        if ($this->isCsrfTokenValid('resolve'.$ticket->getId(), $request->request->get('_token'))) {
            try {
                $ticket->setStatut(Ticket::STATUT_RESOLU);
                $ticket->setDateResolution(new \DateTime());
                $entityManager->flush();

                // Envoyer la notification
                $notificationService->notifyTicketResolved($ticket);

                $this->addFlash('success', 'Le ticket a été marqué comme résolu.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la résolution du ticket.');
            }
        }

        return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
    }

    #[Route("/{id}/change-status", name: "app_ticket_change_status", methods: ["POST"])]
    public function changeStatus(Request $request, Ticket $ticket, EntityManagerInterface $entityManager, NotificationService $notificationService, LoggerInterface $logger): Response
    {
        $logger->info('Début de changeStatus', [
            'ticket_id' => $ticket->getId(),
            'request_method' => $request->getMethod(),
            'current_status' => $ticket->getStatut()
        ]);

        $newStatus = $request->request->get('status');
        $logger->info('Nouveau statut reçu', [
            'new_status' => $newStatus,
            'token_valid' => $this->isCsrfTokenValid('change-status'.$ticket->getId(), $request->request->get('_token'))
        ]);

        if ($newStatus && $this->isCsrfTokenValid('change-status'.$ticket->getId(), $request->request->get('_token'))) {
            try {
                $oldStatus = $ticket->getStatut();
                $logger->info('Changement de statut du ticket', [
                    'ticket_id' => $ticket->getId(),
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ]);

                $ticket->setStatut($newStatus);
                
                if ($newStatus === 'résolu') {
                    $ticket->setDateResolution(new \DateTime());
                }
                
                $entityManager->flush();

                $logger->info('Statut du ticket mis à jour, envoi de la notification', [
                    'ticket_id' => $ticket->getId(),
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ]);

                // Envoyer la notification de changement de statut
                $notificationService->notifyStatusChange($ticket, $oldStatus, $newStatus);

                $logger->info('Notification envoyée avec succès');

                $this->addFlash('success', 'Le statut du ticket a été mis à jour.');
            } catch (\Exception $e) {
                $logger->error('Erreur lors du changement de statut : ' . $e->getMessage(), [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                $this->addFlash('error', 'Une erreur est survenue lors du changement de statut.');
            }
        } else {
            $logger->warning('Tentative de changement de statut invalide', [
                'new_status' => $newStatus,
                'token_present' => (bool)$request->request->get('_token')
            ]);
        }

        return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
    }

    #[Route("/{id}/cloturer", name: "app_ticket_close", methods: ["POST"])]
    public function close(Request $request, Ticket $ticket, EntityManagerInterface $entityManager, NotificationService $notificationService): Response
    {
        if ($this->isCsrfTokenValid('close'.$ticket->getId(), $request->request->get('_token'))) {
            try {
                $ticket->setStatut(Ticket::STATUT_CLOTURE);
                $ticket->setDateResolution(new \DateTime());
                $entityManager->flush();
                
                // Envoyer la notification
                $notificationService->notifyTicketResolved($ticket);
                
                $this->addFlash('success', 'Le ticket a été clôturé avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la clôture du ticket.');
            }
        }

        return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
    }

    /**
     * Notifie l'utilisateur par email lors de la création ou de la modification d'un ticket.
     */
    public function notifyUser(Ticket $ticket): void
    {
        // Logique pour envoyer un email à l'utilisateur
        // Par exemple, utiliser un service de messagerie pour envoyer un email
    }
}
