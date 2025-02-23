<?php

namespace App\Controller;

use App\Entity\Technicien;
use App\Form\TechnicienType;
use App\Repository\TechnicienRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(TicketRepository $ticketRepository, TechnicienRepository $technicienRepository): Response
    {
        $tickets = $ticketRepository->findAll();
        $techniciens = $technicienRepository->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'tickets' => $tickets,
            'techniciens' => $techniciens,
        ]);
    }

    #[Route('/technicien/new', name: 'admin_technicien_new', methods: ['GET', 'POST'])]
    public function newTechnicien(
        Request $request, 
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $technicien = new Technicien();
        $form = $this->createForm(TechnicienType::class, $technicien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le mot de passe en clair
            $plainPassword = $form->get('plainPassword')->getData();
            
            // Hasher le mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $technicien,
                $plainPassword
            );
            
            // Définir le mot de passe hashé
            $technicien->setPassword($hashedPassword);
            
            // Ajouter le rôle TECHNICIEN
            $technicien->addRole('ROLE_TECHNICIEN');
            
            // Persister et sauvegarder
            $entityManager->persist($technicien);
            $entityManager->flush();

            $this->addFlash('success', 'Le technicien a été créé avec succès.');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/technicien/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/technicien/{id}/edit', name: 'admin_technicien_edit', methods: ['GET', 'POST'])]
    public function editTechnicien(
        Request $request, 
        Technicien $technicien, 
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $form = $this->createForm(TechnicienType::class, $technicien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si un nouveau mot de passe est fourni
            if ($plainPassword = $form->get('plainPassword')->getData()) {
                $hashedPassword = $passwordHasher->hashPassword(
                    $technicien,
                    $plainPassword
                );
                $technicien->setPassword($hashedPassword);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le technicien a été modifié avec succès.');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/technicien/edit.html.twig', [
            'technicien' => $technicien,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/technicien/{id}/delete', name: 'admin_technicien_delete', methods: ['POST'])]
    public function deleteTechnicien(
        Request $request, 
        Technicien $technicien, 
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$technicien->getId(), $request->request->get('_token'))) {
            $entityManager->remove($technicien);
            $entityManager->flush();
            $this->addFlash('success', 'Le technicien a été supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_dashboard');
    }
}
