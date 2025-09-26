<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\RoleConverter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';
    public const AUTHOR_USER_REFERENCE = 'author-user';
    public const READER_USER_REFERENCE = 'reader-user';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly RoleConverter $roleConverter,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('cs_CZ');

        // Admin User (password: password1)
        $admin = new User('Admin User', 'admin@example.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password1'));
        $admin->setRole($this->roleConverter->toSystemRole('admin'));
        $manager->persist($admin);

        // Author User (password: password2)
        $author = new User('Author User', 'author@example.com');
        $author->setPassword($this->passwordHasher->hashPassword($author, 'password2'));
        $author->setRole($this->roleConverter->toSystemRole('author'));
        $manager->persist($author);

        // Reader User (password: password3)
        $reader = new User('Reader User', 'reader@example.com');
        $reader->setPassword($this->passwordHasher->hashPassword($reader, 'password3'));
        $reader->setRole($this->roleConverter->toSystemRole('reader'));
        $manager->persist($reader);

        // Random users (password: password4)
        for ($i = 0; $i < 10; ++$i) {
            $user = new User($faker->name(), $faker->safeEmail());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password4'));
            $user->setRole($this->roleConverter->toSystemRole('reader'));
            $manager->persist($user);
        }

        $manager->flush();

        $this->addReference(self::ADMIN_USER_REFERENCE, $admin);
        $this->addReference(self::AUTHOR_USER_REFERENCE, $author);
        $this->addReference(self::READER_USER_REFERENCE, $reader);
    }
}
