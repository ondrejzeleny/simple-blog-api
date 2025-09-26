<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User entity.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    private int $id;

    #[ORM\Column(length: 180)]
    /** @var non-empty-string */
    private string $email;

    #[ORM\Column(length: 255)]
    private string $role;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private string $password;

    #[ORM\Column(length: 255)]
    private string $name;

    /**
     * @var Collection<int, Article>
     */
    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $articles;

    public function __construct(string $name, string $email)
    {
        if ('' === trim($email)) {
            throw new \InvalidArgumentException('User email cannot be empty.');
        }

        $this->name = $name;
        $this->email = $email;
        $this->articles = new ArrayCollection();
    }

    /**
     * Get user ID.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get user email.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set user email.
     */
    public function setEmail(string $email): static
    {
        if ('' === trim($email)) {
            throw new \InvalidArgumentException('User email cannot be empty.');
        }

        $this->email = $email;

        return $this;
    }

    /**
     * Get user identifier.
     *
     * @return non-empty-string
     */
    public function getUserIdentifier(): string
    {
        if ('' === $this->email) {
            throw new \LogicException('User email cannot be empty.');
        }

        return $this->email;
    }

    /**
     * Get user roles.
     */
    public function getRoles(): array
    {
        return [
            'ROLE_USER',
            $this->role,
        ];
    }

    /**
     * Get user role.
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * Set user role.
     */
    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get user password.
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set user password.
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    /**
     * Get user name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set user name.
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get user articles.
     *
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    /**
     * Add article to user.
     */
    public function addArticle(Article $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setAuthor($this);
        }

        return $this;
    }

    /**
     * Remove article from user.
     */
    public function removeArticle(Article $article): static
    {
        $this->articles->removeElement($article);

        return $this;
    }
}
