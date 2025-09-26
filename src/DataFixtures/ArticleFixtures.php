<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Fixtures for articles.
 */
class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Load article fixtures.
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('cs_CZ');

        $adminUser = $this->getReference(UserFixtures::ADMIN_USER_REFERENCE, User::class);
        $authorUser = $this->getReference(UserFixtures::AUTHOR_USER_REFERENCE, User::class);

        // Admin articles
        for ($i = 0; $i < 5; ++$i) {
            $article = new Article();
            $article->setTitle($faker->sentence(6));
            $article->setContent($faker->paragraph());
            $article->setAuthor($adminUser);
            $manager->persist($article);
        }

        // Author articles
        for ($i = 0; $i < 5; ++$i) {
            $article = new Article();
            $article->setTitle($faker->sentence(6));
            $article->setContent($faker->paragraph());
            $article->setAuthor($authorUser);
            $manager->persist($article);
        }

        $manager->flush();
    }

    /**
     * Get fixture dependencies.
     *
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
