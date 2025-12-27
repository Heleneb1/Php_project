<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use DateTimeImmutable;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        // Création des épisodes pour chaque saison
for ($programId = 1; $programId <= 10; $programId++) {
    for ($seasonId = 1; $seasonId <= 5; $seasonId++) {
        for ($j = 1; $j <= 10; $j++) {
            $episode = new Episode();
            $episode->setNumber($j);
            $episode->setTitle($faker->sentence());
            $episode->setSynopsis($faker->paragraphs(1, true));
            $createdAt = DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months'));
            $episode->setCreatedAt($createdAt);
            
            // Obtention d'une référence à la saison correspondante
            $seasonReference = 'season_' . $programId . '_' . $seasonId;
            $episode->setSeason($this->getReference($seasonReference));
            $episode->setDuration($faker->numberBetween(40, 90));

            $manager->persist($episode);
            $this->addReference('episode_' . $programId . '_' . $seasonId . '_' . $j, $episode);
        }
    }
}


        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            SeasonFixtures::class,
        ];
    }
}

// <?php

// namespace App\DataFixtures;

// use App\Entity\Episode;
// use Doctrine\Bundle\FixturesBundle\Fixture;
// use Doctrine\Persistence\ObjectManager;
// use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// class EpisodeFixtures extends Fixture implements DependentFixtureInterface
// {
//     public function load(ObjectManager $manager): void
//     {
//         // Create the first episode
//         $episode1 = new Episode();
//         $episode1->setTitle('Welcome to the Playground');
//         $episode1->setNumber(1);
//         $episode1->setSynopsis('In the city of Piltover, orphans Vi and Powder stoke the fires of rebellion.');
//         $episode1->setSeason($this->getReference('season1_Arcane'));
//         $manager->persist($episode1);

//         // Create the second episode
//         $episode2 = new Episode();
//         $episode2->setTitle('Some Mysteries Are Better Left Unsolved');
//         $episode2->setNumber(2);
//         $episode2->setSynopsis('With tensions running high, Powder goes on a dangerous mission.');
//         $episode2->setSeason($this->getReference('season1_Arcane'));
//         $manager->persist($episode2);

//         // Create the third episode
//         $episode3 = new Episode();
//         $episode3->setTitle('The Base Violence Necessary for Change');
//         $episode3->setNumber(3);
//         $episode3->setSynopsis('The sisters struggle to navigate the hostile world they live in.');
//         $episode3->setSeason($this->getReference('season1_Arcane'));
//         $manager->persist($episode3);

        

//         // Flush all the new episodes to the database
//         $manager->flush();
//     }

//     public function getDependencies(): array
//     {
//         return [
//             SeasonFixtures::class,  // Make sure to define SeasonFixtures
//         ];
//     }
// }
