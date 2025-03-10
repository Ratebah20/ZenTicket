<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Personne;

class AuthController extends AbstractController
{
    private $jwtManager;
    private $passwordHasher;
    private $entityManager;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ) {
        $this->jwtManager = $jwtManager;
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    #[Route('/api/auth', name: 'api_auth', methods: ['POST'])]
    public function getTokenUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['username']) || !isset($data['password'])) {
            return $this->json([
                'message' => 'Les champs username et password sont requis'
            ], Response::HTTP_BAD_REQUEST);
        }

        $email = $data['username'];
        $password = $data['password'];

        // Recherche de l'utilisateur par email
        $user = $this->entityManager->getRepository(Personne::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->json([
                'message' => 'Utilisateur non trouvé'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Vérification du mot de passe
        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            return $this->json([
                'message' => 'Mot de passe incorrect'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Génération du token JWT
        $token = $this->jwtManager->create($user);

        return $this->json([
            'token' => $token
        ]);
    }

    #[Route('/api/user', name: 'api_user', methods: ['GET'])]
    public function getUserInfo(TokenStorageInterface $tokenStorage): JsonResponse
    {
        $user = $tokenStorage->getToken()->getUser();

        if (!$user instanceof UserInterface) {
            return $this->json([
                'message' => 'Utilisateur non authentifié'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'email' => $user->getUserIdentifier(),
            'roles' => $user->getRoles()
        ]);
    }
}
