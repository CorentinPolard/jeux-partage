<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\PaginatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/user')]
final class AdminUserController extends AbstractController
{
    #[Route('', name: 'app_admin_users')]
    public function index(PaginatorService $paginatorService, UserRepository $userRepository, Request $request): Response
    {
        $paginationDatas = $paginatorService->initPagination($userRepository, 50, 'u', $request);

        return $this->render('admin_user/index.html.twig', [
            'users' => $paginationDatas['items'],
            'page' => $paginationDatas['page'],
            'maxPage' => $paginationDatas['maxPage'],
            'route' => 'app_admin_users',
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
            return $this->redirectToRoute('app_admin_users');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_users');
    }
}
