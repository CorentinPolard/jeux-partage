<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepository;
use App\Service\PaginatorService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/games')]
final class GameController extends AbstractController
{
    #[Route('', name: 'app_games')]
    public function index(PaginatorService $paginatorService, GameRepository $gameRepository, Request $request): Response
    {
        $paginationDatas = $paginatorService->initPagination($gameRepository, 20, 'g', $request);

        return $this->render('game/games-list.html.twig', [
            'games' => $paginationDatas['items'],
            'page' => $paginationDatas['page'],
            'maxPage' => $paginationDatas['maxPage'],
            'route' => 'app_games',
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
