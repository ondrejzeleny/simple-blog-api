<?php

namespace App\Controller;

use App\Dto\UserCreateDto;
use App\Entity\User;
use App\Factory\UserFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

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
     *
     * @param UserProviderInterface<User> $userProvider
     */
    #[Route('/auth/register', name: 'api_auth_register', methods: ['POST'])]
    public function register(
        #[MapRequestPayload] UserCreateDto $dto,
        UserProviderInterface $userProvider,
    ): JsonResponse {
        try {
            $userProvider->loadUserByIdentifier($dto->email);

            return $this->json(['error' => 'User with this email already exists.'], Response::HTTP_CONFLICT);
        } catch (UserNotFoundException) {
        }

        $user = $this->userFactory->createFromDto($dto);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'User registered successfully.'], Response::HTTP_CREATED);
    }
}
