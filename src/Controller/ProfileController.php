<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\ChangePasswordType;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/profile')]
final class ProfileController extends AbstractController
{
    #[Route('/{id}', name: 'app_profile', requirements: ['id' => '\d+'])]
    public function index(User $user): Response
    {
        return $this->render('profile/index.html.twig', [
            'user' => $user,
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
            return $this->redirectToRoute('app_profile', ['id' => $user->getId()]);
        }

        return $this->render('profile/edit-profile.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/password', name: 'app_edit_password')]
    public function editPassword(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, Request $request): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();
            if (!$passwordHasher->isPasswordValid($user, $oldPassword)) {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
                return $this->redirectToRoute('app_edit_password');
            }

            $newPassword = $form->get('plainPassword')->getData();
            if ($passwordHasher->isPasswordValid($user, $newPassword)) {
                $this->addFlash('error', 'Le nouveau mot de passe doit être différent de l\'ancien.');
                return $this->redirectToRoute('app_edit_password');
            }

            $hashedNewPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedNewPassword);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe mis à jour avec succès !');
            return $this->redirectToRoute('app_profile', ['id' => $user->getId()]);
        }

        return $this->render('profile/edit-password.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/delete', name: 'app_delete_profile')]
    public function delete(EntityManagerInterface $entityManager, Request $request): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer un administrateur.');
            return $this->redirectToRoute('app_profile', ['id' => $user->getId()]);
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
        return $this->redirectToRoute('app_profile', ['id' => $user->getId()]);
    }
}
