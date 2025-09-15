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
    #[Route('/create/{id}', name: 'app_create_message', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function createMessage(
        Event $event,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $message = new Message();
        $message->setUser($this->getUser());
        $message->setEvent($event);
        $message->setContent($request->request->get('content'));
        $message->setCreatedAt(new \DateTime());

        $entityManager->persist($message);
        $entityManager->flush();

        return $this->json([
            'messageDatas' => [
                'content' => $message->getContent(),
                'createdAt' => $message->getCreatedAt()->format('d-m-Y H:i'),
                'user' => [
                    'username' => $message->getUser()->getFullName(),
                    'profilePicture' => $message->getUser()->getProfilePicture(),
                ],
            ],
        ]);
    }

    #[Route('/delete/{id}', name: 'app_delete_message', requirements: ['id' => '\d+'])]
    public function deleteMessage(Message $message, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($message);
        $entityManager->flush();

        return $this->json(['message' => "Message supprimé."]);
    }

    #[Route('/fetch/{id}', name: 'app_fetch_messages', requirements: ['id' => '\d+'])]
    public function fetchMessages(Event $event): Response
    {
        $messages = $event->getMessages();

        return $this->json(['messages' => $messages]);
    }
}
