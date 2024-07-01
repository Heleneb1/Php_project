<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Actor;
use Faker\Factory;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 10; $i++) {
            $actor = new Actor();
            $actor->setName($faker->name());

            // Choose 3 distinct random programs for each actor
            $chosenPrograms = [];
            while (count($chosenPrograms) < 3) {
                $programReference = 'program_' . $faker->numberBetween(1, 10); // Adjust 10 to match the number of your program fixtures
                if (!in_array($programReference, $chosenPrograms)) {
                    $actor->addProgram($this->getReference($programReference));
                    $chosenPrograms[] = $programReference;
                }
            }

            $manager->persist($actor);
            $this->addReference('actor_' . $i, $actor);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProgramFixtures::class, // Adjust with the correct name of your ProgramFixtures class
        ];
    }
}
