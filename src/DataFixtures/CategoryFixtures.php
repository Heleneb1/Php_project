<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Faker\Factory;

class CategoryFixtures extends Fixture

{
    public function load(ObjectManager $manager): void
    {
    $faker = Factory::create();

    for($i = 0; $i < 6; $i++) {
        $category = new Category();
        $category->setName($faker->word());
        $manager->persist($category);

        $this->addReference('category_' . $i, $category);
    }

    $manager->flush();

    // public const CATEGORIES = [
    //     'Action',
    //     'Aventure',
    //     'Animation',
    //     'Fantastique',
    //     'Horreur',
        
    // ];

    // public function load(ObjectManager $manager)
    // {
    //     foreach (self::CATEGORIES as $categoryName) {
    //         $category = new Category();
    //         $category->setName($categoryName);
    //         $manager->persist($category);
    //         $this->addReference('category_' . $categoryName, $category);
    //     }
    //     $manager->flush();
    // }
}
}