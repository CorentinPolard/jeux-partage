<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/categories')]
final class AdminCategoryController extends AbstractController
{
    #[Route('', name: 'app_admin_categories')]
    public function index(CategoryRepository $categoryRepository, Request $request): Response
    {
        $limit = 50;
        $page = max(1, $request->query->getInt('page', 1));
        $categories = $categoryRepository->paginate($page, $limit, 'c');

        $maxPages = min(1, ceil($categories->count() / $limit));

        return $this->render('admin_category/index.html.twig', [
            'categories' => $categories,
            'page' => $page,
            'maxPages' => $maxPages,
            'route' => 'app_admin_categories',
        ]);
    }

    #[Route('/create', name: 'app_admin_create_category')]
    public function createCategory(EntityManagerInterface $entityManager, Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_categories');
        }

        return $this->render('admin_category/create-category.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_admin_edit_category', requirements: ['id' => '\d+'])]
    public function editCategory(Category $category, EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_categories');
        }

        return $this->render('admin_category/create-category.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_admin_delete_category', requirements: ['id' => '\d+'])]
    public function deleteCategory(Category $category, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($category);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_categories');
    }
}
