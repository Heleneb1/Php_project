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
use Symfony\Componet\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Service\ProgramDuration;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    // #[Route('/', name: 'index')]
    // public function index(ProgramRepository $programRepository): Response
    // {
    //     $programs = $programRepository->findAll();

    //     return $this->render(
    //         'program/index.html.twig',
    //         ['programs' => $programs]
    //     );
    // }
    #[Route('/', name: 'index')]
    public function index(RequestStack $requestStack, ProgramRepository $programRepository): Response
    {
        // Récupère la session à partir de la RequestStack
        $session = $requestStack->getSession();
    
        // Vérifie si la session a une clé 'total'. Si non, initialise à 0
        if (!$session->has('total')) {
            $session->set('total', 0);
        }
    
        // Récupère tous les programmes à partir du repository ProgramRepository
        $programs = $programRepository->findAll();
    
        // Récupère la valeur actuelle de 'total' dans la session
        $total = $session->get('total');
    
        // Rend le template Twig 'program/index.html.twig' avec les variables 'total' et 'programs'
        return $this->render(
            'program/index.html.twig',
            ['total' => $total, 'programs' => $programs]
        );
    }
    #[Route('/new', name: 'program_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($program->getTitle())->lower(); // Convert slug to lower case
            $program->setSlug($slug);
            $entityManager->persist($program);
            $entityManager->flush();

            // Define the success flash message once the form is submitted, valid and the data inserted in the database
            $this->addFlash('success', 'The new program has been created');

            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }    // #[Route('/show/{id<^[0-9]+$>}', name: 'show')]
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
    // #[Route('/{id}', name: 'show')]
    // public function show(Program $program): Response
    // {
    //     return $this->render('program/show.html.twig', ['program' => $program]);
    // }
    #[Route('/{slug}', name: 'show')]
    public function show(
        #[MapEntity(mapping: ['slug' => 'slug'])] Program $program,//utilisation de l'attribut mapping pour indiquer que le paramètre slug de la route correspond à l'attribut slug de l'entité Program
        ProgramDuration $programDuration
    ): Response {
        // Calculate the duration using the ProgramDuration service
        $duration = $programDuration->calculate($program);

        // Render the template and pass the program and duration variables
        return $this->render('program/show.html.twig', [
            'program' => $program,
            'duration' => $duration,
        ]);
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
    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Program $program, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'The program has been updated');

            // return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
            return $this->redirectToRoute('program_show', ['id' => $program->getId()]);
        }

        return $this->render('program/edit.html.twig', [
            'program' => $program, // Add a comma here
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $program->getId(), $request->request->get('_token'))) {
            $entityManager->remove($program);
            $entityManager->flush();
            $this->addFlash('danger', 'The program has been deleted');
        }

        return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
    }
   

}
