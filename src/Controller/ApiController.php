<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ApiController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Bienvenue sur l\'API ZenTicket',
            'user' => $this->getUser()->getUserIdentifier(),
            'roles' => $this->getUser()->getRoles(),
        ]);
    }

    #[Route('/test', name: 'test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return $this->json([
            'message' => 'Bienvenue sur l\'API',
            'user' => $this->getUser()->getUserIdentifier(),
        ]);
    }
}
