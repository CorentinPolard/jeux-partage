<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/messages')]
final class MessageController extends AbstractController
{
    #[Route('/delete/{id}', name: 'app_delete_message', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteMessage(Message $message, EntityManagerInterface $entityManager, Request $request): Response
    {
        $event = $message->getEvent();

        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete_message_' . $message->getId(), $submittedToken) && $this->getUser() === $message->getUser()) {
            $entityManager->remove($message);
            $entityManager->flush();
        } else {
            $this->addFlash('error', "Vous ne pouvez pas supprimer ce commentaire.");
        }

        return $this->redirectToRoute('app_show_event', ['id' => $event->getId()]);
    }
}
