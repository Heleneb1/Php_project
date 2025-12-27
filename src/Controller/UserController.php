<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use App\Entity\Program;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;


class UserController extends AbstractController
{
    #[Route('/my-profile', name: 'user_profile')]
    #[IsGranted("ROLE_USER")]
    public function profile(ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $userId = $user->getId();

        $watchlist = $doctrine->getRepository(Program::class)->findWatchlist($userId);

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'watchlist' => $watchlist,
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Vérifiez si l'utilisateur connecté est bien celui dont on veut éditer le profil
        $currentUser = $this->getUser();
        if ($currentUser !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette page.');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
             // Réinitialisez l'objet File
            $user->setAvatarFile(null);

            return $this->redirectToRoute('user_profile'); // Redirige vers la page de profil après modification
        }

        return $this->render('user/edit.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
        
    }
    #[Route('/{id}/edit/avatar', name: 'user_edit_avatar', methods: ['POST'])]
    public function editAvatar(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();
        if ($currentUser !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette page.');
        }

        // Récupérer le fichier envoyé
        $avatarFile = $request->files->get('avatarFile');

        if ($avatarFile) {
            // Assurez-vous que vous utilisez bien le VichUploader
            $user->setAvatarFile($avatarFile);

            $entityManager->flush();
            $user->setAvatarFile(null);

            $this->addFlash('success', 'Votre image de profil a été mise à jour.');

            return $this->redirectToRoute('user_profile');
        }

        $this->addFlash('error', 'Veuillez sélectionner une image.');

        return $this->redirectToRoute('user_profile');
    }
}