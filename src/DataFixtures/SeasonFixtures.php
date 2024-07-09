<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $programs = $manager->getRepository('App\Entity\Program')->findAll();
        // Création des saisons
        for ($programId = 1; $programId <= count($programs); $programId++) {
            // Création de 5 saisons pour chaque programme
            $startYear = $faker->year();
            for ($i = 1; $i <= 5; $i++) {
                $season = new Season();
                $season->setNumber($i);
                $season->setYear($startYear + $i);// Génération d'une année aléatoire pour chaque saison 
                $season->setDescription($faker->paragraphs(1, true));
                // Obtention d'une référence à un programme
                $programReference = 'program_' . $programId;
                $season->setProgram($this->getReference($programReference));

                $manager->persist($season);
                $this->addReference('season_' . $programId . '_' . $i, $season);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProgramFixtures::class,
        ];
    }
}

// namespace App\DataFixtures;

// use App\Entity\Season;
// use Doctrine\Bundle\FixturesBundle\Fixture;
// use Doctrine\Persistence\ObjectManager;
// use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// class SeasonFixtures extends Fixture implements DependentFixtureInterface
// {
//     public function load(ObjectManager $manager)
//     {
//         $season1 = new Season();
//         $season1->setNumber(1);
//         $season1->setYear(2021);
//         $season1->setDescription('The first season of the series Arcane');
//         $season1->setProgram($this->getReference('program_Arcane'));
//         $manager->persist($season1);

//         $this->addReference('season1_Arcane', $season1);

//         $manager->flush();
//     }

//     public function getDependencies()
//     {
//         return [
//             ProgramFixtures::class,
//         ];
//     }
// }
