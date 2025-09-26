<?php

namespace App\Security;

use App\Entity\Article;
use App\Entity\User;
use App\Service\RoleConverter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Article|class-string<Article>>
 */
class ArticleVoter extends Voter
{
    public const VIEW = 'USER_VIEW';
    public const EDIT = 'USER_EDIT';
    public const CREATE = 'USER_CREATE';
    public const DELETE = 'USER_DELETE';

    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::CREATE, self::DELETE])) {
            return false;
        }

        if (self::CREATE === $attribute) {
            return Article::class === $subject;
        }

        return $subject instanceof Article;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Article $article */
        $article = $subject;

        return match ($attribute) {
            self::CREATE => $this->canCreate($token),
            self::VIEW => $this->canView(),
            self::EDIT => $this->canEdit($token, $article, $user),
            self::DELETE => $this->canDelete($token, $article, $user),
            default => false,
        };
    }

    private function canView(): bool
    {
        return true;
    }

    private function canCreate(TokenInterface $token): bool
    {
        return $this->accessDecisionManager->decide($token, [
            RoleConverter::ROLE_ADMIN,
            RoleConverter::ROLE_AUTHOR,
        ]);
    }

    private function canEdit(TokenInterface $token, Article $article, User $user): bool
    {
        if ($this->accessDecisionManager->decide($token, [RoleConverter::ROLE_ADMIN])) {
            return true;
        }

        return $user === $article->getAuthor();
    }

    private function canDelete(TokenInterface $token, Article $article, User $user): bool
    {
        return $this->canEdit($token, $article, $user);
    }
}
