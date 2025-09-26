<?php

namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class AuthLoginController extends AbstractController
{
    public function __construct(
        private readonly JWTTokenManagerInterface $JWTManager,
    ) {
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function index(#[CurrentUser] ?User $user): JsonResponse
    {
        if (null === $user) {
            return $this->json(['message' => 'Missing credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->JWTManager->create($user);

        return $this->json([
            'user' => $user->getUserIdentifier(),
            'token' => $token,
        ]);
    }
}
