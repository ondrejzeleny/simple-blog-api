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
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
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
            self::EDIT => $this->canEdit($token, $article, $user),
            self::DELETE => $this->canDelete($token, $article, $user),
            default => false,
        };
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
