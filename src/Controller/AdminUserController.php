<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/user')]
final class AdminUserController extends AbstractController
{
    #[Route('', name: 'app_admin_user')]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $limit = 50;
        $page = max(1, $request->query->getInt('page', 1));
        $users = $userRepository->paginate($page, $limit, 'u');
        $maxPage = max(1, ceil($users->count() / $limit));

        return $this->render('admin_user/index.html.twig', [
            'users' => $users,
            'page' => $page,
            'maxPage' => $maxPage,
            'route' => 'app_admin_user',
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
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer un administrateur.');
            return $this->redirectToRoute('app_admin_user');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_user');
    }
}
