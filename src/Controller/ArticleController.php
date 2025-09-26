<?php

namespace App\Controller;

use App\Dto\ArticleCreateDto;
use App\Dto\ArticleUpdateDto;
use App\Entity\Article;
use App\Entity\User;
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

class ArticleController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ArticleTransformer $articleTransformer,
    ) {
    }

    #[Route('/api/articles', name: 'api_article_index', methods: ['GET'])]
    #[IsGranted(ArticleVoter::VIEW, subject: 'article')]
    public function index(ArticleRepository $articleRepository): JsonResponse
    {
        $articles = $articleRepository->findAll();
        $data = array_map([$this->articleTransformer, 'transform'], $articles);

        return $this->json($data);
    }

    #[Route('/api/articles/{id}', name: 'api_article_show', methods: ['GET'])]
    #[IsGranted(ArticleVoter::VIEW, subject: 'article')]
    public function show(Article $article): JsonResponse
    {
        return $this->json($this->articleTransformer->transform($article));
    }

    #[Route('/api/articles', name: 'api_article_create', methods: ['POST'])]
    #[IsGranted(ArticleVoter::CREATE, subject: Article::class)]
    public function create(#[MapRequestPayload] ArticleCreateDto $dto): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $article = new Article();
        $article->setTitle($dto->title);
        $article->setContent($dto->content);
        $article->setAuthor($user);

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $this->json($this->articleTransformer->transform($article), Response::HTTP_CREATED);
    }

    #[Route('/api/articles/{id}', name: 'api_article_update', methods: ['PUT'])]
    #[IsGranted(ArticleVoter::EDIT, subject: 'article')]
    public function update(Article $article, #[MapRequestPayload] ArticleUpdateDto $dto): JsonResponse
    {
        $article->setTitle($dto->title);
        $article->setContent($dto->content);
        $article->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return $this->json($this->articleTransformer->transform($article));
    }

    #[Route('/api/articles/{id}', name: 'api_article_delete', methods: ['DELETE'])]
    #[IsGranted(ArticleVoter::DELETE, subject: 'article')]
    public function delete(Article $article): JsonResponse
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
