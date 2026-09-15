<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IdentityController extends AbstractController
{
    #[Route('/identity', name: 'app_identity')]
    public function index(): Response
    {
        return $this->render('identity/index.html.twig', [
            'controller_name' => 'IdentityController',
        ]);
    }
}
