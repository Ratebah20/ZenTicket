<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JwtTestController extends AbstractController
{
    #[Route('/jwt-test', name: 'app_jwt_test')]
    public function index(): Response
    {
        return $this->render('jwt_test/index.html.twig', [
            'csrf_token' => $this->container->get('security.csrf.token_manager')->getToken('authenticate')->getValue(),
        ]);
    }
}
