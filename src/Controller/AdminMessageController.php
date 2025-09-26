<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\EventRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/messages')]
final class AdminMessageController extends AbstractController
{
    #[Route('', name: 'app_admin_messages')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAllAndMessages();
        return $this->render('admin_message/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_admin_delete_message', requirements: ['id' => '\d+'])]
    public function deleteMessage(Message $message, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($message);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_messages');
    }
}
