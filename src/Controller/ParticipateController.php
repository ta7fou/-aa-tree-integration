<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipateController extends AbstractController
{
    #[Route('/participate', name: 'app_participate')]
    public function index(): Response
    {
        return $this->render('participate/index.html.twig', [
            'controller_name' => 'ParticipateController',
        ]);
    }
}
