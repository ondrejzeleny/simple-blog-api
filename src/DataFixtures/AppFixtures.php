<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Base fixtures class.
 */
class AppFixtures extends Fixture
{
    /**
     * Load fixtures.
     */
    public function load(ObjectManager $manager): void
    {
        $manager->flush();
    }
}
