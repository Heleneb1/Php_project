<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // This is now handled by Doctrine's dependency management
        // $categoryFixtures = new CategoryFixtures();
        // $categoryFixtures->load($manager);
        
        // $programFixtures = new ProgramFixtures();
        // $programFixtures->load($manager);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
            ProgramFixtures::class,  // Make sure all dependent fixtures are listed here
        ];
    }
}
