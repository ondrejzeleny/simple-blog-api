<?php

namespace App\DataFixtures;

use App\Factory\UserFactoryInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Fixtures for users.
 */
class UserFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';
    public const AUTHOR_USER_REFERENCE = 'author-user';
    public const READER_USER_REFERENCE = 'reader-user';

    /**
     * Create fixtures.
     */
    public function __construct(
        private readonly UserFactoryInterface $userFactory,
    ) {
    }

    /**
     * Load user fixtures.
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('cs_CZ');

        // Admin User (password: password1)
        $admin = $this->userFactory->createFromParameters('Admin User', 'admin@example.com', 'password1', 'admin');
        $manager->persist($admin);

        // Author User (password: password2)
        $author = $this->userFactory->createFromParameters('Author User', 'author@example.com', 'password2', 'author');
        $manager->persist($author);

        // Reader User (password: password3)
        $reader = $this->userFactory->createFromParameters('Reader User', 'reader@example.com', 'password3', 'reader');
        $manager->persist($reader);

        // Random users (password: password4)
        for ($i = 0; $i < 10; ++$i) {
            $user = $this->userFactory->createFromParameters($faker->name(), $faker->safeEmail(), 'password4', 'reader');
            $manager->persist($user);
        }

        $manager->flush();

        $this->addReference(self::ADMIN_USER_REFERENCE, $admin);
        $this->addReference(self::AUTHOR_USER_REFERENCE, $author);
        $this->addReference(self::READER_USER_REFERENCE, $reader);
    }
}
