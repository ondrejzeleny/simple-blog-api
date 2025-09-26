<?php

namespace App\Controller;

use App\Dto\UserCreateDto;
use App\Dto\UserUpdateDto;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\RoleConverter;
use App\Transformer\UserTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserTransformer $userTransformer,
        private readonly RoleConverter $roleConverter,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    #[Route('/users', name: 'api_user_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        $data = array_map([$this->userTransformer, 'transform'], $users);

        return $this->json($data);
    }

    #[Route('/users/{id}', name: 'api_user_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function show(User $user): JsonResponse
    {
        return $this->json($this->userTransformer->transform($user));
    }

    #[Route('/users', name: 'api_user_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(#[MapRequestPayload] UserCreateDto $dto): JsonResponse
    {
        $user = new User($dto->name, $dto->email);
        $user->setRole($this->roleConverter->toSystemRole($dto->role));
        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json($this->userTransformer->transform($user), Response::HTTP_CREATED);
    }

    #[Route('/users/{id}', name: 'api_user_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(User $user, #[MapRequestPayload] UserUpdateDto $dto): JsonResponse
    {
        if (!is_null($dto->name)) {
            $user->setName($dto->name);
        }

        if (!is_null($dto->email)) {
            $user->setEmail($dto->email);
        }

        if (!is_null($dto->role)) {
            $user->setRole($this->roleConverter->toSystemRole($dto->role));
        }

        $this->entityManager->flush();

        return $this->json($this->userTransformer->transform($user));
    }

    #[Route('/users/{id}', name: 'api_user_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(User $user): JsonResponse
    {
        if (!$user->getArticles()->isEmpty()) {
            return $this->json(['error' => 'Cannot delete user with articles.'], Response::HTTP_CONFLICT);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
