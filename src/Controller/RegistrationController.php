<?php

// src/Controller/RegistrationController.php
namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ): Response {
        // Vérifier si l'utilisateur est déjà connecté
        if ($this->getUser()) {
            $this->addFlash('error', 'Vous êtes déjà connecté');
            return $this->redirectToRoute('app_home');
        }

        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        
        try {
            $form->handleRequest($request);
            
            $logger->info('Formulaire soumis', [
                'isSubmitted' => $form->isSubmitted(),
                'method' => $request->getMethod(),
                'data' => $request->request->all()
            ]);

            if ($form->isSubmitted() && $form->isValid()) {
                $logger->info('Traitement du formulaire valide');
                
                // Convertir l'email en minuscules
                $email = strtolower($form->get('email')->getData());
                $user->setEmail($email);
                
                $logger->info('Email défini', ['email' => $email]);

                // Récupérer et hasher le mot de passe
                $plainPassword = $form->get('plainPassword')->getData();
                if (!$plainPassword) {
                    throw new \Exception('Le mot de passe est obligatoire');
                }
                
                $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
                
                $logger->info('Mot de passe hashé et défini');

                // Ajouter le rôle utilisateur par défaut
                $user->addRole('ROLE_USER');
                
                $logger->info('Rôle ajouté');

                // Persister l'utilisateur
                $entityManager->persist($user);
                $entityManager->flush();
                
                $logger->info('Utilisateur persisté en base de données', ['id' => $user->getId()]);

                $this->addFlash('success', 'Votre compte a été créé avec succès !');
                return $this->redirectToRoute('app_login');
            } elseif ($form->isSubmitted()) {
                $logger->warning('Formulaire invalide', [
                    'errors' => $this->getFormErrors($form)
                ]);
                foreach ($this->getFormErrors($form) as $error) {
                    $this->addFlash('error', $error);
                }
            }
        } catch (\Exception $e) {
            $logger->error('Erreur lors de la création du compte', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->addFlash('error', 'Une erreur est survenue lors de la création de votre compte: ' . $e->getMessage());
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
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

    #[Route('/register/check-email', name: 'app_register_check_email', methods: ['POST'])]
    public function checkEmail(Request $request, UtilisateurRepository $utilisateurRepository): JsonResponse
    {
        $email = strtolower($request->request->get('email'));
        
        if (!$email) {
            return new JsonResponse([
                'exists' => false,
                'message' => 'Veuillez fournir une adresse email'
            ]);
        }

        $user = $utilisateurRepository->findOneBy(['email' => $email]);

        return new JsonResponse([
            'exists' => $user !== null,
            'message' => $user ? 'Cette adresse email est déjà utilisée' : null
        ]);
    }
}
