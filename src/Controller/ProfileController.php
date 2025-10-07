<?php

namespace App\Controller;

use App\Form\UserType;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/profile')]
final class ProfileController extends AbstractController
{
    #[Route('', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/edit', name: 'app_edit_profile')]
    public function edit(ImageUploadService $imageUploadService, EntityManagerInterface $entityManager, Request $request): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newImage = $form->get('profilePicture')->getData();
            if ($newImage) {
                $newFileName = $imageUploadService->processFile($user->getProfilePicture(), $newImage, 'profil-pictures');
                $user->setProfilePicture($newFileName);
            }
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès !');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit-profile.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/delete', name: 'app_delete_profile')]
    public function delete(EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer un administrateur.');
            return $this->redirectToRoute('app_profile');
        }

        // Vérification du token CSRF
        if ($this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            // Déconnexion après suppression
            $this->container->get('security.token_storage')->setToken(null);
            $request->getSession()->invalidate();

            $this->addFlash('success', 'Votre compte a été supprimé avec succès.');
            return $this->redirectToRoute('app_home');
        }

        $this->addFlash('error', 'Échec de la suppression du compte.');
        return $this->redirectToRoute('app_profile');
    }
}
