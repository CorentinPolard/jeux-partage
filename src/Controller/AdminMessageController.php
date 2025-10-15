<?php

namespace App\Controller;

use App\Entity\Message;
use App\Service\PaginatorService;
use App\Repository\EventRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/messages')]
final class AdminMessageController extends AbstractController
{
    #[Route('', name: 'app_admin_messages')]
    public function index(EventRepository $eventRepository, Request $request): Response
    {
        $limit = 10;
        $page = max(1, $request->query->getInt('page', 1));
        $events = $eventRepository->findAllAndMessages($page, $limit);
        $maxPage = max(1, ceil($eventRepository->countEventsWithMessages() / $limit));

        return $this->render('admin_message/index.html.twig', [
            'events' => $events,
            'page' => $page,
            'maxPage' => $maxPage,
            'route' => 'app_admin_messages',
        ]);
    }

    #[Route('/delete/{id}', name: 'app_admin_delete_message', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteMessage(Message $message, EntityManagerInterface $entityManager, Request $request): Response
    {
        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete_category_' . $message->getId(), $submittedToken)) {
            $entityManager->remove($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_messages');
    }
}
