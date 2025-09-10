<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/messages')]
final class AdminMessageController extends AbstractController
{
    #[Route('', name: 'app_admin_messages')]
    public function index(): Response
    {
        return $this->render('admin_message/index.html.twig', [
            'controller_name' => 'AdminMessageController',
        ]);
    }
}
