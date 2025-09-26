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

#[Route('/api/users')]
#[IsGranted('ROLE_ADMIN')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserTransformer $userTransformer,
        private readonly RoleConverter $roleConverter,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    #[Route('', name: 'api_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        $data = array_map([$this->userTransformer, 'transform'], $users);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'api_user_show', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        return $this->json($this->userTransformer->transform($user));
    }

    #[Route('', name: 'api_user_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] UserCreateDto $dto): JsonResponse
    {
        $user = new User($dto->name, $dto->email);
        $user->setRole($this->roleConverter->toSystemRole($dto->role));
        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json($this->userTransformer->transform($user), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_user_update', methods: ['PUT'])]
    public function update(User $user, #[MapRequestPayload] UserUpdateDto $dto): JsonResponse
    {
        $user->setName($dto->name);
        $user->setEmail($dto->email);
        $user->setRole($this->roleConverter->toSystemRole($dto->role));

        $this->entityManager->flush();

        return $this->json($this->userTransformer->transform($user));
    }

    #[Route('/{id}', name: 'api_user_delete', methods: ['DELETE'])]
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
