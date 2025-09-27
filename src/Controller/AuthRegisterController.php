<?php

namespace App\Controller;

use App\Dto\UserCreateDto;
use App\Factory\UserFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for user registration.
 */
class AuthRegisterController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserFactoryInterface $userFactory,
    ) {
    }

    /**
     * Register new user.
     */
    #[Route('/auth/register', name: 'api_auth_register', methods: ['POST'])]
    public function register(#[MapRequestPayload] UserCreateDto $dto): JsonResponse
    {
        try {
            $user = $this->userFactory->createFromDto($dto);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->json(['message' => 'User registered successfully.'], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        }
    }
}
