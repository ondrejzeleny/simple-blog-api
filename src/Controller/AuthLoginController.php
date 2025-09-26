<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller for user authentication.
 */
class AuthLoginController extends AbstractController
{
    /**
     * Handle user login.
     */
    #[Route('/auth/login', name: 'api_auth_login', methods: ['POST'])]
    public function index(): JsonResponse
    {
        throw new \LogicException('This code should not be reached!');
    }
}
