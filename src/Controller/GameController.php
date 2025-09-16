<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/games')]
final class GameController extends AbstractController
{
    #[Route('', name: 'app_games')]
    public function index(GameRepository $gameRepository, Request $request): Response
    {
        // Nombre de jeux par page
        $limit = 20;
        // Notre variable page est égale à la valeur du paramètre 'page' dans l'URL
        $page = $request->query->getInt('page', 1);

        $games = $gameRepository->paginate($page, $limit, "g");

        $maxPages = ceil($games->count() / $limit);

        if ($page < 1) {
            return $this->redirectToRoute('app_games', ['page' => 1]);
        }
        if ($page > $maxPages) {
            return $this->redirectToRoute('app_games', ['page' => $maxPages]);
        }

        return $this->render('game/games-list.html.twig', [
            'games' => $games,
            'maxPages' => $maxPages,
            'page' => $page,
        ]);
    }

    #[Route('/show/{id}', name: 'app_show_game', requirements: ['id' => '\d+'])]
    public function showGame(Game $game): Response
    {
        return $this->render('game/single-game.html.twig', [
            'game' => $game,
        ]);
    }
}
