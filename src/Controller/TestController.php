<?php

// src/Controller/TestController.php

namespace App\Controller;

use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(ManagerRegistry $doctrine): Response
    {
        // Récupérer un utilisateur avec l'ID 1
        $utilisateur = $doctrine
            ->getRepository(Utilisateur::class)
            ->find(2); 

        if (!$utilisateur) {
            // Si l'utilisateur n'existe pas
            return new Response('Utilisateur non trouvé');
        }

        // Récupérer tous les tickets associés à cet utilisateur
        $tickets = $utilisateur->getTickets();

        // Si l'utilisateur n'a pas de tickets
        if (!$tickets) {
            return new Response('Aucun ticket trouvé pour cet utilisateur');
        }

        // Rendre la vue Twig avec les informations récupérées
        return $this->render('test/index.html.twig', [
            'utilisateur' => $utilisateur,
            'tickets' => $tickets,
        ]);
    }
}


