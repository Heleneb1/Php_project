<?php

namespace App\Service;
use App\Entity\Program;

class ProgramDuration
{
   // permet de calculer la durée de visionnage totale d'une série.
    public function calculate(Program $program): string
    {
        $seasons = $program->getSeasons();
        $totalDuration = 0;
        foreach ($seasons as $season) {
            $episodes = $season->getEpisodes();
            foreach ($episodes as $episode) {
                $totalDuration += $episode->getDuration();
            }
        }
        $totalDuration = $totalDuration / 60;
        $totalDuration = round($totalDuration, 2);
        $totalDuration = $totalDuration.' heures';

        return $totalDuration;
        // return 'comming soon...';
    }
}