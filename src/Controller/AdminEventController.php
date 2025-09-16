<?php

namespace App\Controller;

use DateTime;
use App\Entity\Event;
use App\Entity\Message;
use App\Form\EventType;
use App\Form\MessageType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        $event->setOrganizer($this->getUser());

        $form = $this->createForm(EventType::class, $event, [
            'show_admin_fields' => true, // Afficher les champs admin
        ]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $event->setCreatedAt(new DateTime());

            // Date dans le futur
            if ($event->getEventAt() < new DateTime()) {
                $this->addFlash('error', 'La date de l\'événement doit être dans le futur.');

                return $this->render('admin_event/create-event.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Si l'organisateur a été changé dans le formulaire, on met à jour
            if ($event->getOrganizer() !== $form->getData()->getOrganizer()) {
                $event()->setOrganizer($form->getData()->getOrganizer());
            }

            // On s'assure que l'organisateur n'est pas dans les participants
            if ($event->getParticipants()->contains($event->getOrganizer())) {
                $event->removeParticipant($event->getOrganizer());
            }

            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_show_event', ['id' => $event->getId()]);
        }

        return $this->render('admin_event/create-event.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{id}', name: 'app_admin_show_event', requirements: ['id' => '\d+'])]
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

            return $this->redirectToRoute('app_admin_show_event', ['id' => $event->getId()]);
        }


        return $this->render('admin_event/single-event.html.twig', [
            'event' => $event,
            'jsonCoordinates' => $jsonCoordinates,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/edit/{id}', name: 'app_admin_edit_event')]
    public function editEvent(Event $event, EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(EventType::class, $event, [
            'show_admin_fields' => true, // Afficher les champs admin
        ]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $event->setUpdatedAt(new DateTime());

            if ($event->getEventAt() < new DateTime()) {
                $this->addFlash('error', 'La date de l\'événement doit être dans le futur.');

                return $this->render('admin_event/edit-event.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Si l'organisateur a été changé dans le formulaire, on met à jour
            if ($event->getOrganizer() !== $form->getData()->getOrganizer()) {
                $event()->setOrganizer($form->getData()->getOrganizer());
            }

            // On s'assure que l'organisateur n'est pas dans les participants
            if ($event->getParticipants()->contains($event->getOrganizer())) {
                $event->removeParticipant($event->getOrganizer());
            }

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
