<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function index(#[CurrentUser] ?User $user): Response
    {
        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->getTokenFromCredentials($user->getUsername(), $user->getPassword());
        
        return $this->json([
            'user'  => $user->getUserIdentifier(),
            'token' => $token,
        ]);
    }

    private function getTokenFromCredentials($username, $password)
    {
        // Ici, vous devez implémenter la logique pour générer un token à partir des identifiants de l'utilisateur.
        // Pour l'instant, je vais simplement retourner une chaîne vide.
        return '';
    }
}
