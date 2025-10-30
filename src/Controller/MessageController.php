<?php

namespace App\Controller;

use DateTime;
use App\Entity\Message;
use App\Repository\EventRepository;
use Symfony\Component\Mercure\Update;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/messages')]
final class MessageController extends AbstractController
{
    #[Route('/create', name: 'app_create_message', methods: ['POST'])]
    public function createMessage(
        EventRepository $eventRepository,
        NormalizerInterface $normalizer,
        EntityManagerInterface $entityManager,
        HubInterface $hub,
        CsrfTokenManagerInterface $csrfTokenManager,
        Request $request
    ): Response {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        // Vérification CSRF
        $csrfToken = $data['_csrf_token'] ?? '';
        if (!$this->isCsrfTokenValid('message', $csrfToken)) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], 400);
        }

        // Récupération de l'event
        $eventId = $data['event_id'] ?? null;
        $event = $eventRepository->find($eventId);
        if (!$eventId || !$event) {
            return new JsonResponse(['error' => 'Event missing'], 400);
        }

        // Vérification de l'accès utilisateur à l'évènement
        if ($event->getOrganizer() !== $user && !$event->getParticipants()->contains($user)) {
            return new JsonResponse(['error' => 'Unauthorized to post on this event'], 403);
        }

        // Validation du contenu
        $content = trim($data['content'] ?? '');
        if (empty($content)) {
            return new JsonResponse(['error' => 'Message content cannot be empty'], 400);
        }

        // Création et sauvegarde du message
        $message = new Message();
        $topic = "/event/$eventId/messages";

        $message->setContent($data['content']);
        $message->setUser($this->getUser());
        $message->setCreatedAt(new DateTime());
        $message->setEvent($event);

        $entityManager->persist($message);
        $entityManager->flush();

        // Génération du token pour suppression 
        $csrfDeleteToken = $csrfTokenManager->getToken('delete_message_' . $message->getId())->getValue();

        // Préparation des données à envoyer via Mercure
        $payload = [
            'message' => $normalizer->normalize(
                $message,
                'json',
                ['groups' => ['message_with_user']]
            ),
            'token' => $csrfDeleteToken,
            'action' => 'create',
        ];

        // Publication du message via Mercure
        $update = new Update(
            $topic,
            json_encode($payload)
        );

        $hub->publish($update);

        return new JsonResponse(['status' => 'Message created'], Response::HTTP_CREATED);
    }


    #[Route('/delete/{id}', name: 'app_delete_message', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteMessage(
        Message $message,
        EntityManagerInterface $entityManager,
        HubInterface $hub,
        Request $request
    ): JsonResponse {
        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete_message_' . $message->getId(), $submittedToken) && $this->getUser() === $message->getUser()) {

            $eventId = $message->getEvent()->getId();

            $topic = "/event/$eventId/messages";
            $update = new Update(
                $topic,
                json_encode([
                    'action' => 'delete',
                    'id' => $message->getId(),
                ])
            );
            $hub->publish($update);

            $entityManager->remove($message);
            $entityManager->flush();

            return new JsonResponse(['status' => 'Message deleted'], Response::HTTP_OK);
        } else {
            return new JsonResponse(['error' => 'Invalid CSRF token or unauthorized'], Response::HTTP_FORBIDDEN);
        }
    }
}
