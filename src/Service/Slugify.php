<?php

namespace App\Service;
use App\Entity\Program;

use Symfony\Component\String\Slugger\SluggerInterface;

class Slugify
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function slugify(string $string): string
    {
        $slug = $this->slugger->slug($string)->lower();
        echo $slug.PHP_EOL;
        return $slug;
    }
    
}