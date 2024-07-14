<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use App\Repository\CategoryRepository;

#[AsTwigComponent]
final class Navbar
{
    public function __construct(
        private CategoryRepository $categoryRepository
    ) {
     
    }
    public function getCategories(): array
    {
        return $this->categoryRepository->findBy([], ['name' => 'ASC']);
        
    }
}
