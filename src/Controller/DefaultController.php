<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
  
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        $name = 'Mon site de séries';
        return $this->render('index.html.twig', [
            'page_title' =>'Mon site de séries',
            'name' => $name,
        ]);
    }
}

