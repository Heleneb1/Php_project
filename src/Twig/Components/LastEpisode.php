<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use App\Repository\EpisodeRepository;

#[AsTwigComponent]
final class LastEpisode
{
    public function __construct(
        private EpisodeRepository $episodeRepository
    )
    {
    }
    public function getLastEpisodes(): array
    {
       return $this->episodeRepository->findBy([], ['createdAt' => 'DESC'], 3);
    }
}
