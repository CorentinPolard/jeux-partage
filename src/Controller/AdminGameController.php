<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/games')]
final class AdminGameController extends AbstractController
{
    #[Route('', name: 'app_admin_games')]
    public function index(): Response
    {
        return $this->render('admin_game/index.html.twig', [
            'controller_name' => 'AdminGameController',
        ]);
    }
}
