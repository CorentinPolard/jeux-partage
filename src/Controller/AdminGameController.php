<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
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
        SluggerInterface $slugger,
        EntityManagerInterface $entityManager,
        #[Autowire('%kernel.project_dir%/public/images/uploads/games')] string $imagesDirectory,
        Request $request
    ): Response {
        $game = new Game();

        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // Récupère le nom de l'image et enlève l'extension
                $originalFileName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move($imagesDirectory, $newFileName);

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
        SluggerInterface $slugger,
        EntityManagerInterface $entityManager,
        #[Autowire('%kernel.project_dir%/public/images/uploads/games')] string $imagesDirectory,
        Request $request
    ): Response {
        $oldImageName = $game->getImageFileName();

        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newImageFile = $form->get('image')->getData();

            if ($newImageFile) {
                $originalFileName = pathinfo($newImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $newImageFile->guessExtension();

                if ($oldImageName && file_exists($imagesDirectory . '/' . $oldImageName)) {
                    unlink($imagesDirectory . '/' . $oldImageName);
                }

                $newImageFile->move($imagesDirectory, $newFileName);

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
        if ($imageName && file_exists($imagesDirectory . '/' . $imageName)) {
            unlink($imagesDirectory . '/' . $imageName);
        }

        $entityManager->remove($game);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_games');
    }
}
