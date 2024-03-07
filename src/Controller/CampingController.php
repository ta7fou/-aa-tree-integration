<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampingController extends AbstractController
{
    #[Route('/camping', name: 'app_camping')]
    public function index(): Response
    {
        return $this->render('camping/index.html.twig', [
            'controller_name' => 'CampingController',
        ]);
    }
}
