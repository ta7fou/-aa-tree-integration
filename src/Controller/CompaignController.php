<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompaignController extends AbstractController
{
    #[Route('/compaign', name: 'app_compaign')]
    public function index(): Response
    {
        return $this->render('compaign/index.html.twig', [
            'controller_name' => 'CompaignController',
        ]);
    }
}
