<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contrôleur gérant l'authentification des utilisateurs
 * 
 * Ce contrôleur gère les fonctionnalités de connexion et de déconnexion
 * des utilisateurs dans l'application.
 */
class SecurityController extends AbstractController
{
    /**
     * Affiche et gère le formulaire de connexion
     * 
     * @param AuthenticationUtils $authenticationUtils Utilitaire d'authentification Symfony
     * @return Response Page de connexion avec gestion des erreurs
     */
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupère l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // Dernier nom d'utilisateur saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        // Debug
        if ($error) {
            dump($error);  // Ceci affichera l'erreur dans la barre de debug
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Gère la déconnexion de l'utilisateur
     * 
     * Cette méthode est interceptée par le système de sécurité de Symfony
     * et n'a pas besoin d'implémenter de logique.
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Cette méthode peut être vide - elle sera interceptée par la clé de déconnexion de votre pare-feu.');
    }
}
