<?php

namespace App\DataFixtures;

use App\Entity\Program;
use App\Service\Slugify;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

use Faker\Factory;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    private $slugger;

    public function __construct(Slugify $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // L'objectif est de créer 10 séries qui appartiendront à une catégorie au hasard
        for ($i = 1; $i <= 10; $i++) {
            $program = new Program();
            $title = $faker->sentence(); // Génère un titre aléatoire avec Faker
            $program->setTitle($title);
            $slug = $this->slugger->slugify($title);
            $program->setSlug($slug);
            $program->setSynopsis($faker->paragraphs(2, true));
            $program->setOwner($this->getReference('user_' . $faker->numberBetween(0, 5)));
            $program->setCategory($this->getReference('category_' . $faker->numberBetween(1, 5)));

            $manager->persist($program);
            $this->addReference('program_' . $i, $program);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
// namespace App\DataFixtures;

// use App\Entity\Program;
// use Doctrine\Bundle\FixturesBundle\Fixture;
// use Doctrine\Persistence\ObjectManager;
// use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// class ProgramFixtures extends Fixture implements DependentFixtureInterface
// {
//     const PROGRAMS = [
//         [
//             'title' => 'Walking Dead',
//             'synopsis' => 'Des zombies envahissent la terre.',
//             'category' => 'category_Action',
//         ],
//         [
//             'title' => 'The Mandalorian',
//             'synopsis' => 'La série se déroule après la chute de l\'Empire et avant l\'émergence du Premier Ordre. Elle raconte les voyages dans les contrées les plus éloignées de la Galaxie d\'un tireur solitaire, loin de l’autorité de la Nouvelle République.',
//             'category' => 'category_Action',
//         ],
//         [
//             'title' => 'The Haunting of Hill House',
//             'synopsis' => 'Plusieurs frères et sœurs qui, enfants, ont grandi dans la demeure qui allait devenir la maison hantée la plus célèbre des États-Unis. Adultes désormais, et contraints de se réunir à cause d\'une tragédie, ils devront affronter les fantômes de leur propre passé.',
//             'category' => 'category_Horreur',
//         ],
//         [
//             'title' => 'The Witcher',
//             'synopsis' => 'Le sorceleur Geralt, un chasseur de monstres mutant, se bat pour trouver sa place dans un monde où les humains se révèlent souvent plus vicieux que les bêtes.',
//             'category' => 'category_Fantastique',
//         ],
//         [
//             'title' => 'Chernobyl',
//             'synopsis' => 'La catastrophe de Tchernobyl est racontée par le biais des destins de ceux qui ont causé l\'explosion, ceux qui ont lutté pour éviter un pire désastre et ceux qui ont souffert de ses conséquences.',
//             'category' => 'category_Action',
//         ],
//         [
//             'title' => 'Game of Thrones',
//             'synopsis' => 'Dans le royaume de Westeros, plusieurs familles nobles se disputent le trône.',
//             'category' => 'category_Aventure',
//         ],
//         [
//             'title' => 'Arcane',
//             'synopsis' => 'Arcane est une série d\'animation en 3D inspirée de l\'univers de League of Legends. Elle se déroule dans la ville de Piltover et dans les bas-fonds de Zaun, deux districts séparés par leur niveau technologique et leur niveau de richesse.',
//             'category' => 'category_Animation',
//         ],
//     ];

//     public function load(ObjectManager $manager)
//     {
//         foreach (self::PROGRAMS as $programData) {
//             $program = new Program();
//             $program->setTitle($programData['title']);
//             $program->setSynopsis($programData['synopsis']);
//             $program->setCategory($this->getReference($programData['category']));
//             // ... set other program's properties

//             $manager->persist($program);

//             // Set a unique reference for each program
//             $this->addReference('program_' . $programData['title'], $program);
//         }

//         $manager->flush();
//     }

//     public function getDependencies()
//     {
//         return [
//             CategoryFixtures::class,
//         ];
//     }
// }
