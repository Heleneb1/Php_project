<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; // Correct annotation import
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ProgramType;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();

        return $this->render(
            'program/index.html.twig',
            ['programs' => $programs]
        );
    }
    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($program);
            $entityManager->flush();

            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    // #[Route('/show/{id<^[0-9]+$>}', name: 'show')]
    // public function show(int $id, ProgramRepository $programRepository): Response
    // {
    //     $program = $programRepository->findOneBy(['id' => $id]);

    //     if (!$program) {
    //         throw $this->createNotFoundException(
    //             'No program with id : ' . $id . ' found in program\'s table.'
    //         );
    //     }

    //     return $this->render('program/show.html.twig', [
    //         'program' => $program,
    //     ]);
    // }
    #[Route('/{id}/', name: 'show')]
    public function show(Program $program): Response
    {
        return $this->render('program/show.html.twig', ['program' => $program]);
    }

    // #[Route('/{programId}/season/{seasonId}', name: 'show_season')]
    // public function showSeason(
    //     int $programId,
    //     int $seasonId,
    //     ProgramRepository $programRepository,
    //     SeasonRepository $seasonRepository
    // ): Response {
    //     $program = $programRepository->findOneBy(['id' => $programId]);

    //     if (!$program) {
    //         throw $this->createNotFoundException(
    //             'No program with id : ' . $programId . ' found in program\'s table.'
    //         );
    //     }

    //     $season = $seasonRepository->findOneBy(['id' => $seasonId, 'program' => $program]);

    //     if (!$season) {
    //         throw $this->createNotFoundException(
    //             'No season with id : ' . $seasonId . ' found for program id : ' . $programId
    //         );
    //     }

    //     return $this->render('program/show_season.html.twig', [
    //         'program' => $program,
    //         'season' => $season,
    //     ]);
    // }
    #[Route('/{programId}/season/{seasonId}', name: 'show_season')]
    public function showSeason(
    #[MapEntity(mapping: ['programId' => 'id'])] Program $program,
    #[MapEntity(mapping: ['seasonId' => 'id'])] Season $season,
    ): Response
    {
        return $this->render('program/show_season.html.twig', [
            'program' => $program,
            'season' => $season,
        ]);
    }
    #[Route('/{programId}/season/{seasonId}/episode/{episodeId}', name: 'episode_show')]
    public function progamEpisodeShow(
    #[MapEntity(mapping: ['programId' => 'id'])] Program $program,
    #[MapEntity(mapping: ['seasonId' => 'id'])] Season $season,
    #[MapEntity(mapping: ['episodeId' => 'id'])] Episode $episode,
    ): Response
    {
        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
        ]);
    }
   

}
