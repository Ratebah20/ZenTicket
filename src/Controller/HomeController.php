<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur de la page d'accueil
 * 
 * Gère l'affichage et les fonctionnalités de la page d'accueil
 * de l'application.
 */
class HomeController extends AbstractController
{
    /**
     * Affiche la page d'accueil
     * 
     * @return Response La vue de la page d'accueil
     */
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'title' => 'Bienvenue sur mon site Symfony',
        ]);
    }
}
