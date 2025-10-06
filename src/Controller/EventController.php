<?php

namespace App\Controller;

use DateTime;
use App\Entity\Event;
use App\Entity\Message;
use App\Form\EventsFilterType;
use App\Form\EventType;
use App\Form\MessageType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/events')]
final class EventController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer
    ) {}

    #[Route('', name: 'app_events')]
    public function index(EventRepository $eventRepository, Request $request): Response
    {
        // Pagination
        $limit = 10;
        $page = $request->query->getInt('page', 1);

        // Filtre
        $city = $request->query->get('city');
        $departmentNumber = $request->query->get('departmentNumber');

        // Formulaire de filtrage
        $form = $this->createForm(EventsFilterType::class, [
            'city' => $city,
            'departmentNumber' => $departmentNumber
        ]);
        $form->handleRequest($request);

        $filterBy = $form->isSubmitted() && $form->isValid() ? $form->getData() : [
            'city' => $city,
            'departmentNumber' => $departmentNumber
        ];

        if (in_array($filterBy['departmentNumber'], ["2A", "2B"])) {
            $filterBy['departmentNumber'] = 20;
        }

        // Récupération des évènements selon les filtres (si il y en a)
        $events = $eventRepository->paginate($page, $limit, "e", new DateTime(), 'eventAt', $filterBy['city'], $filterBy['departmentNumber']);

        // Calcul du nombre de pages pour la pagination
        $maxPage = $events->count() > 0 ? ceil($events->count() / $limit) : 1;
        if ($page < 1) {
            return $this->redirectToRoute('app_events', ['page' => 1, 'city' => $filterBy['city'], 'departmentNumber' => $filterBy['departmentNumber']]);
        }
        if ($page > $maxPage) {
            return $this->redirectToRoute('app_events', ['page' => $maxPage, 'city' => $filterBy['city'], 'departmentNumber' => $filterBy['departmentNumber']]);
        }

        return $this->render('event/events-list.html.twig', [
            'events' => $events,
            'maxPage' => $maxPage,
            'page' => $page,
            'route' => 'app_events',
            'eventForm' => $form->createView(),
        ]);
    }

    #[Route('/show/{id}', name: 'app_show_event', requirements: ['id' => '\d+'])]
    public function showEvent(Event $event, EntityManagerInterface $entityManager, Request $request): Response
    {
        $jsonCoordinates = $this->serializer->serialize(
            $event,
            'json',
            [
                'groups' => ['coordinates'],
            ]
        );

        $message = new Message();

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setUser($this->getUser());
            $message->setCreatedAt(new DateTime());
            $message->setEvent($event);

            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('app_show_event', ['id' => $event->getId()]);
        }

        return $this->render('event/single-event.html.twig', [
            'event' => $event,
            'jsonCoordinates' => $jsonCoordinates,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/create', name: 'app_create_event')]
    public function createEvent(EntityManagerInterface $entityManager, Request $request): Response
    {
        $event = new Event();

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $event->setOrganizer($this->getUser());

            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_show_event', ['id' => $event->getId()]);
        }

        return $this->render('event/create-event.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_edit_event')]
    public function editEvent(Event $event, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($event->getOrganizer() === $this->getUser()) {

            $form = $this->createForm(EventType::class, $event);
            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {
                $event->setUpdatedAt(new DateTime());
                $entityManager->flush();
                return $this->redirectToRoute('app_show_event', ['id' => $event->getId()]);
            }

            return $this->render('event/edit-event.html.twig', [
                'event' => $event,
                'form' => $form->createView(),
            ]);
        } else {
            $this->addFlash('error', "Vous n'êtes pas autorisé à modifier cet évènement.");
            return $this->redirectToRoute('app_events');
        }
    }

    #[Route('/delete/{id}', name: 'app_delete_event', requirements: ['id' => '\d+'])]
    public function deleteEvent(Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($event->getOrganizer() === $this->getUser()) {
            $entityManager->remove($event);
            $entityManager->flush();
        } else {
            $this->addFlash('error', "Vous n'êtes pas autorisé à supprimer cet évènement.");
        }

        return $this->redirectToRoute('app_events');
    }

    #[Route('/register/{id}', name: 'app_register_event', requirements: ['id' => '\d+'])]
    public function register(Event $event, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($event->isFull()) {
            $this->addFlash('error', "Cet évènement est complet.");
            return $this->redirectToRoute('app_events');
        } elseif ($event->getOrganizer() === $user) {
            $this->addFlash('error', "Vous ne pouvez pas vous inscrire à un évènement que vous organisez.");
            return $this->redirectToRoute('app_my_events');
        }

        if (!$event->getParticipants()->contains($user)) {
            $event->addParticipant($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_show_event', ['id' => $event->getId()]);
        } else {
            $event->removeParticipant($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_events');
        }
    }

    #[Route('/my-events', name: 'app_my_events')]
    public function myEvents(EventRepository $eventRepository): Response
    {
        $user = $this->getUser();

        $organizedEvents = $eventRepository->findBy(
            ['organizer' => $user],
            ['eventAt' => 'DESC']
        );

        $joinedEvents = $eventRepository->findByOneParticipant($user);

        return $this->render('event/my-events.html.twig', [
            'organizedEvents' => $organizedEvents,
            'joinedEvents' => $joinedEvents,
        ]);
    }
}
