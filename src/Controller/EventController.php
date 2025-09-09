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

#[Route('/events')]
final class EventController extends AbstractController
{
    #[Route('', name: 'app_events')]
    public function index(EventRepository $eventRepository, Request $request): Response
    {
        $limit = 10;

        $page = $request->query->getInt('page', 1);

        $events = $eventRepository->paginate($page, $limit, "e");

        $maxPages = ceil($events->count() / $limit);


        if ($page < 1) {
            return $this->redirectToRoute('app_events', ['page' => 1]);
        }
        if ($page > $maxPages) {
            return $this->redirectToRoute('app_events', ['page' => $maxPages]);
        }

        return $this->render('event/events-list.html.twig', [
            'events' => $events,
            'maxPages' => $maxPages,
            'page' => $page,
            'redirectTo' => 'app_events',
        ]);
    }

    #[Route('/show/{id}', name: 'app_show_event', requirements: ['id' => '\d+'])]
    public function showEvent(Event $event): Response
    {

        // Messagerie ici 

        return $this->render('event/single-event.html.twig', [
            'event' => $event
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

            return $this->redirectToRoute('app_events');
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

                $entityManager->persist($event);
                $entityManager->flush();

                return $this->redirectToRoute('app_show_event', ['id' => $event->getId()]);
            }

            return $this->render('event/edit-event.html.twig', [
                'event' => $event,
                'form' => $form->createView(),
            ]);
        } else {
            $this->addFlash('error', "Vous n'êtes pas autorisé à modifier cet événement.");
            return $this->redirectToRoute('app_events');
        }
    }

    #[Route('/delete/{id}', name: 'app_delete_event', requirements: ['id' => '\d+'])]
    public function deleteEvent(Event $event, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($event->getOrganizer() === $this->getUser()) {
            $entityManager->remove($event);
            $entityManager->flush();
        } else {
            $this->addFlash('error', "Vous n'êtes pas autorisé à supprimer cet événement.");
        }

        return $this->redirectToRoute('app_events');
    }

    #[Route('/register/{id}', name: 'app_register_event', requirements: ['id' => '\d+'])]
    public function register(Event $event, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (
            $event->getOrganizer() !== $user
            && !$event->getParticipants()->contains($user)
            && !$event->isFull()
        ) {
            $event->addParticipant($user);
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_show_event', ['id' => $event->getId()]);
        } else {
            $this->addFlash('error', "Vous ne pouvez pas vous inscrire à cet événement. Il est complet ou vous êtes déjà inscrit.");
            return $this->redirectToRoute('app_events');
        }
    }

    #[Route('/unregister/{id}', name: 'app_unregister_event', requirements: ['id' => '\d+'])]
    public function unregister(Event $event, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (
            $event->getOrganizer() !== $user
            && $event->getParticipants()->contains($user)
        ) {
            $event->removeParticipant($user);
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_events');
        } else {
            $this->addFlash('error', "Vous ne pouvez pas vous désinscrire de cet événement. Si vous ête l'organisateur supprimez le, ou vous n'êtes déjà pas inscrit.");
            return $this->redirectToRoute('app_events');
        }
    }
}
