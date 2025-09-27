<?php

namespace App\Controller;

use App\Dto\ArticleCreateDto;
use App\Dto\ArticleUpdateDto;
use App\Entity\User;
use App\Factory\ArticleFactoryInterface;
use App\Repository\ArticleRepository;
use App\Security\ArticleVoter;
use App\Transformer\ArticleTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller for managing articles.
 */
class ArticleController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ArticleTransformer $articleTransformer,
        private readonly ArticleRepository $articleRepository,
        private readonly ArticleFactoryInterface $articleFactory,
    ) {
    }

    /**
     * Get all articles.
     */
    #[Route('/articles', name: 'api_article_index', methods: ['GET'])]
    #[IsGranted('ROLE_READER')]
    public function index(): JsonResponse
    {
        $articles = $this->articleRepository->findAll();
        $data = array_map([$this->articleTransformer, 'transform'], $articles);

        return $this->json($data);
    }

    /**
     * Get single article.
     */
    #[Route('/articles/{id}', name: 'api_article_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_READER')]
    public function show(int $id): JsonResponse
    {
        $article = $this->articleRepository->find($id);
        if (!$article) {
            return $this->json(['error' => 'Article not found.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->articleTransformer->transform($article));
    }

    /**
     * Create new article.
     */
    #[Route('/articles', name: 'api_article_create', methods: ['POST'])]
    #[IsGranted('ROLE_AUTHOR')]
    public function create(#[MapRequestPayload] ArticleCreateDto $dto): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $article = $this->articleFactory->createFromDto($dto, $user);

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $this->json($this->articleTransformer->transform($article), Response::HTTP_CREATED);
    }

    /**
     * Update article.
     */
    #[Route('/articles/{id}', name: 'api_article_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_AUTHOR')]
    public function update(int $id, #[MapRequestPayload] ArticleUpdateDto $dto): JsonResponse
    {
        $article = $this->articleRepository->find($id);
        if (!$article) {
            return $this->json(['error' => 'Article not found.'], Response::HTTP_NOT_FOUND);
        }

        // Authorize if user is the author of the article
        $this->denyAccessUnlessGranted(ArticleVoter::EDIT, $article);

        $article = $this->articleFactory->updateFromDto($article, $dto);

        $this->entityManager->flush();

        return $this->json($this->articleTransformer->transform($article));
    }

    /**
     * Delete article.
     */
    #[Route('/articles/{id}', name: 'api_article_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_AUTHOR')]
    public function delete(int $id): JsonResponse
    {
        $article = $this->articleRepository->find($id);
        if (!$article) {
            return $this->json(['error' => 'Article not found.'], Response::HTTP_NOT_FOUND);
        }

        // Authorize if user is the author of the article
        $this->denyAccessUnlessGranted(ArticleVoter::DELETE, $article);

        $this->entityManager->remove($article);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
