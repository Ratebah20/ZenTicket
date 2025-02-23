<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Entity\Utilisateur;
use App\Entity\Technicien;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



#[Route('/profile')]
class ProfileController extends AbstractController
{
    /**
     * Affiche le profil de l'utilisateur connecté
     */
    #[Route('/', name: 'app_profile_index', methods: ['GET'])]
    public function index(): Response
    {
        /** @var UserInterface&PasswordAuthenticatedUserInterface $user */
        $user = $this->getUser();
        if (!$user instanceof Personne) {
            throw new AccessDeniedException('Accès non autorisé.');
        }

        $tickets = [];
        if ($user instanceof Utilisateur || $user instanceof Technicien) {
            $tickets = $user->getTickets();
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'tickets' => $tickets,
        ]);
    }

    /**
     * Permet à l'utilisateur de modifier ses informations personnelles
     */
    #[Route('/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var UserInterface&PasswordAuthenticatedUserInterface $user */
        $user = $this->getUser();
        if (!$user instanceof Personne) {
            throw new AccessDeniedException('Accès non autorisé.');
        }

        // Déterminer le bon type de formulaire selon la classe de l'utilisateur
        $formType = match (true) {
            $user instanceof Utilisateur => \App\Form\UtilisateurType::class,
            $user instanceof Technicien => \App\Form\TechnicienType::class,
            $user instanceof \App\Entity\Administrateur => \App\Form\UtilisateurType::class, // Utiliser UtilisateurType par défaut
            default => throw new \LogicException('Type d\'utilisateur non supporté'),
        };

        $form = $this->createForm($formType, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
            return $this->redirectToRoute('app_profile_index');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet à l'utilisateur de changer son mot de passe
     */
    #[Route('/change-password', name: 'app_profile_change_password', methods: ['GET', 'POST'])]
    public function changePassword(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var UserInterface&PasswordAuthenticatedUserInterface $user */
        $user = $this->getUser();
        if (!$user instanceof Personne || !$user instanceof PasswordAuthenticatedUserInterface) {
            throw new AccessDeniedException('Accès non autorisé.');
        }

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification de l'ancien mot de passe
            if (!$passwordHasher->isPasswordValid($user, $form->get('currentPassword')->getData())) {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
                return $this->redirectToRoute('app_profile_change_password');
            }
            
            // Hash du nouveau mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $user, 
                $form->get('newPassword')->getData()
            );
            
            $user->setPassword($hashedPassword);
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre mot de passe a été modifié avec succès.');
            return $this->redirectToRoute('app_profile_index');
        }

        return $this->render('profile/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche l'historique des tickets de l'utilisateur
     */
    #[Route('/tickets', name: 'app_profile_tickets', methods: ['GET'])]
    public function tickets(): Response
    {
        /** @var UserInterface&PasswordAuthenticatedUserInterface $user */
        $user = $this->getUser();
        
        if (!$user instanceof Utilisateur && !$user instanceof Technicien) {
            throw new AccessDeniedException('Vous n\'avez pas accès aux tickets.');
        }

        return $this->render('ticket/tickets.html.twig', [
            'tickets' => $user->getTickets(),
        ]);
    }
}
