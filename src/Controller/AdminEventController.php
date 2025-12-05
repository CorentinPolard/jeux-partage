<?php

namespace App\Controller;

use DateTime;
use App\Entity\Event;
use DateTimeImmutable;
use App\Entity\Message;
use App\Form\EventType;
use App\Form\MessageType;
use Lcobucci\JWT\Configuration;
use App\Service\PaginatorService;
use App\Repository\EventRepository;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
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
    public function index(PaginatorService $paginatorService, EventRepository $eventRepository, Request $request): Response
    {
        $paginationDatas = $paginatorService->initPagination($eventRepository, 50, 'e', $request);

        return $this->render('admin_event/index.html.twig', [
            'events' => $paginationDatas['items'],
            'page' => $paginationDatas['page'],
            'maxPage' => $paginationDatas['maxPage'],
            'route' => 'app_admin_events',
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
                $this->addFlash('error', 'La date de l\'évènement doit être dans le futur.');

                return $this->render('admin_event/create-event.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Organisateur changé dans le formulaire, on met à jour
            if ($event->getOrganizer() !== $form->getData()->getOrganizer()) {
                $event()->setOrganizer($form->getData()->getOrganizer());
            }

            // Organisateur pas dans participants
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

        $topic = "/event/" . $event->getId() . "/messages";

        // Génération d'un token pour le hub Mercure
        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($_ENV['MERCURE_JWT_SECRET'])
        );
        $now = new DateTimeImmutable();

        $token = $config->builder()
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('mercure', [
                'subscribe' => [$topic],
                'publish' => [],
            ])
            ->getToken($config->signer(), $config->signingKey());

        $mercureToken = $token->toString();


        return $this->render('admin_event/single-event.html.twig', [
            'event' => $event,
            'jsonCoordinates' => $jsonCoordinates,
            'form' => $form->createView(),
            'topic' => $topic,
            'mercureToken' => $mercureToken,
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
                $this->addFlash('error', 'La date de l\'évènement doit être dans le futur.');

                return $this->render('admin_event/edit-event.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Organisateur changé dans le formulaire, on met à jour
            if ($event->getOrganizer() !== $form->getData()->getOrganizer()) {
                $event()->setOrganizer($form->getData()->getOrganizer());
            }

            // Organisateur dans participants
            if ($event->getParticipants()->contains($event->getOrganizer())) {
                $event->removeParticipant($event->getOrganizer());
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_admin_show_event', ['id' => $event->getId()]);
        }

        return $this->render('admin_event/edit-event.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_admin_delete_event', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteEvent(Event $event, EntityManagerInterface $entityManager, Request $request): Response
    {
        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete_event_' . $event->getId(), $submittedToken)) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_events');
    }
}
