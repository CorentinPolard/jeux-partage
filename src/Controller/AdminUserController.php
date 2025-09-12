<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/user')]
final class AdminUserController extends AbstractController
{
    #[Route('', name: 'app_admin_user')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin_user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/show/{id}', name: 'app_admin_show_user', requirements: ['id' => '\d+'])]
    public function showUser(User $user): Response
    {
        return $this->render('admin_user/single-user.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_admin_delete_user', requirements: ['id' => '\d+'])]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_user');
    }
}
