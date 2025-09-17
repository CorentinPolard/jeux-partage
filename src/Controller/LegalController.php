<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/legal')]
final class LegalController extends AbstractController
{
    #[Route('/legal-notice', name: 'app_legal_notice')]
    public function legalNotice(): Response
    {
        return $this->render('legal/legal-notice.html.twig', [
            'controller_name' => 'LegalController',
        ]);
    }

    #[Route('/privacy-policy', name: 'app_privacy_policy')]
    public function privacyPolicy(): Response
    {
        return $this->render('legal/privacy-policy.html.twig', [
            'controller_name' => 'LegalController',
        ]);
    }

    #[Route('/terms-of-service', name: 'app_terms_of_service')]
    public function termsOfService(): Response
    {
        return $this->render('legal/terms-of-service.html.twig', [
            'controller_name' => 'LegalController',
        ]);
    }
}
