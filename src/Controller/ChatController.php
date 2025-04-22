<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ChatController extends AbstractController
{
    #[Route('/chat', name: 'app_chat')]
    #[IsGranted("ROLE_USER")]
    public function index(): Response
    {
        return $this->render('chat/index.html.twig');
    }
}
