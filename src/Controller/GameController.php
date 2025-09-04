<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/game')]
final class GameController extends AbstractController
{
    #[Route('', name: 'app_games')]
    public function index(GameRepository $gameRepository): Response
    {
        $games = $gameRepository->findAll();

        return $this->render('game/index.html.twig', [
            'games' => $games,
        ]);
    }

    #[Route('/add', name: 'app_add_game')]
    public function addGame(SluggerInterface $slugger, EntityManagerInterface $entityManager, Request $request, #[Autowire('%kernel.project_dir%/public/images/uploads/games')] string $imagesDirectory): Response
    {
        $game = new Game();

        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        dump($request->getMethod()); // pour voir si c'est bien POST
        dump($form->isSubmitted());  // vrai/faux

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFileName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move($imagesDirectory, $newFileName);

                $game->setImageFileName($newFileName);
            }

            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('app_games');
        }

        return $this->render('game/add-game.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_edit_game', requirements: ['id' => '\d+'])]
    public function editGame(
        Game $game,
        SluggerInterface $slugger,
        EntityManagerInterface $entityManager,
        #[Autowire('%kernel.project_dir%/public/images/uploads/games')] string $imagesDirectory,
        Request $request
    ): Response {

        // Ajouter un eprotection pour que tout le monde n'ait pas accès à la modif
        // if ($this->getUser() === $game->getAddedBy()) {
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

            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('app_games');
        }

        return $this->render('game/add-game.html.twig', [
            'form' => $form->createView(),
        ]);
        // } else {
        // return $this->redirectToRoute('app_games');
        // }
    }
}
