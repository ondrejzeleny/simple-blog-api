<?php

namespace App\Controller;

use App\Dto\UserCreateDto;
use App\Dto\UserUpdateDto;
use App\Entity\User;
use App\Factory\UserFactoryInterface;
use App\Repository\UserRepository;
use App\Transformer\UserTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller for managing users.
 */
class UserController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserTransformer $userTransformer,
        private readonly UserRepository $userRepository,
        private readonly UserFactoryInterface $userFactory,
    ) {
    }

    /**
     * Get all users.
     */
    #[Route('/users', name: 'api_user_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        $data = array_map([$this->userTransformer, 'transform'], $users);

        return $this->json($data);
    }

    /**
     * Get single user.
     */
    #[Route('/users/{id}', name: 'api_user_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function show(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->userTransformer->transform($user));
    }

    /**
     * Create new user.
     */
    #[Route('/users', name: 'api_user_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(#[MapRequestPayload] UserCreateDto $dto): JsonResponse
    {
        $user = $this->userFactory->createFromDto($dto);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json($this->userTransformer->transform($user), Response::HTTP_CREATED);
    }

    /**
     * Update user.
     */
    #[Route('/users/{id}', name: 'api_user_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(int $id, #[MapRequestPayload] UserUpdateDto $dto): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        $user = $this->userFactory->updateFromDto($user, $dto);

        $this->entityManager->flush();

        return $this->json($this->userTransformer->transform($user));
    }

    /**
     * Delete user.
     */
    #[Route('/users/{id}', name: 'api_user_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        if (!$user->getArticles()->isEmpty()) {
            return $this->json(['error' => 'Cannot delete user with articles.'], Response::HTTP_CONFLICT);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
