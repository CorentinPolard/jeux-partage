<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
use App\Service\ImageUploadService;
use App\Service\PaginatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/games')]
final class AdminGameController extends AbstractController
{
    #[Route('', name: 'app_admin_games')]
    public function index(PaginatorService $paginatorService, GameRepository $gameRepository, Request $request): Response
    {
        $paginationDatas = $paginatorService->initPagination($gameRepository, 50, 'g', $request);

        return $this->render('admin_game/index.html.twig', [
            'games' => $paginationDatas['items'],
            'page' => $paginationDatas['page'],
            'maxPage' => $paginationDatas['maxPage'],
            'route' => 'app_admin_games',
        ]);
    }

    #[Route('/create', name: 'app_admin_create_game')]
    public function createGame(
        EntityManagerInterface $entityManager,
        ImageUploadService $imageUploadService,
        Request $request
    ): Response {
        $game = new Game();

        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newImage = $form->get('image')->getData();
            if ($newImage) {
                $newFileName = $imageUploadService->processFile($game->getImageFileName(), $newImage, 'games');
                $game->setImageFileName($newFileName);
            }

            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_games');
        }

        return $this->render('admin_game/create-game.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{id}', name: 'app_admin_show_game', requirements: ['id' => '\d+'])]
    public function showGame(Game $game): Response
    {
        return $this->render('admin_game/single-game.html.twig', [
            'game' => $game,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_admin_edit_game', requirements: ['id' => '\d+'])]
    public function editGame(
        Game $game,
        EntityManagerInterface $entityManager,
        ImageUploadService $imageUploadService,
        Request $request
    ): Response {

        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newImage = $form->get('image')->getData();
            if ($newImage) {
                $oldImageName = $game->getImageFileName();
                $newFileName = $imageUploadService->processFile($oldImageName, $newImage, 'games');
                $game->setImageFileName($newFileName);
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_admin_show_game', ['id' => $game->getId()]);
        }

        return $this->render('admin_game/edit-game.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_admin_delete_game', requirements: ['id' => '\d+'])]
    public function deleteGame(
        Game $game,
        EntityManagerInterface $entityManager,
        #[Autowire('%kernel.project_dir%/public/images/uploads/games')] string $imagesDirectory,
    ): Response {
        $imageName = $game->getImageFileName();
        if ($imageName != "no-image.svg" && file_exists($imagesDirectory . '/' . $imageName)) {
            unlink($imagesDirectory . '/' . $imageName);
        }

        $entityManager->remove($game);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_games');
    }
}
