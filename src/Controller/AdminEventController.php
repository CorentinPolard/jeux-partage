<?php

namespace App\Controller;

use DateTime;
use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/admin/events')]
final class AdminEventController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer
    ) {}

    #[Route('', name: 'app_admin_events')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();

        return $this->render('admin_event/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/create', name: 'app_admin_create_event')]
    public function createEvent(EntityManagerInterface $entityManager, Request $request): Response
    {
        $event = new Event();

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $event->setOrganizer($this->getUser());

            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_events');
        }

        return $this->render('admin_event/create-event.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{id}', name: 'app_admin_show_event', requirements: ['id' => '\d+'])]
    public function showEvent(Event $event): Response
    {
        $jsonCoordinates = $this->serializer->serialize(
            $event,
            'json',
            [
                'groups' => ['coordinates'],
            ]
        );

        // Messagerie ici 

        return $this->render('admin_event/single-event.html.twig', [
            'event' => $event,
            'jsonCoordinates' => $jsonCoordinates,
        ]);
    }


    #[Route('/edit/{id}', name: 'app_admin_edit_event')]
    public function editEvent(Event $event, EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $event->setUpdatedAt(new DateTime());

            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_show_event', ['id' => $event->getId()]);
        }

        return $this->render('admin_event/edit-event.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_admin_delete_event', requirements: ['id' => '\d+'])]
    public function deleteEvent(Event $event, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($event);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_events');
    }
}
