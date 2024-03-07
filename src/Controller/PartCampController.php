<?php

namespace App\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\PartCamp;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\PartcampType;
use App\Repository\PartCampRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class PartCampController extends AbstractController
{
    #[Route('/participate/{id}', name: 'app_partcamp', methods: ['GET'])]
    public function new($id, Request $request, EntityManagerInterface $entityManager, PartCampRepository $PartCampRepository, SessionInterface $session, Security $security): Response
    {
        $user = $security->getUser();

        // Check if the User is authenticated
        if ($user === null) {
            // Handle the case where the user is not authenticated
            // For example, redirect the user to the login page
            return $this->redirectToRoute('app_login');
        }

        $PartCamp = new PartCamp();

        // If the User is authenticated, populate the PartCamp with User information
        $PartCamp->setUser($user)
                  ->setUserId($user->getId())
                  ->setNomuser($user->getNom())
                  ->setEmailuser($user->getEmail())
                  ->setCampingId($id);

        $form = $this->createForm(PartCampType::class, $PartCamp);
        $form->handleRequest($request);

        // Handle form submission
        if ($form->isSubmitted() && $form->isValid()) {
            // Make sure the user_id is set before persisting
            $PartCamp->setUserId($user->getId());

            $entityManager->persist($PartCamp);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('partcamp/index.html.twig', [
            'PartCamp' => $PartCamp,
            'form' => $form->createView(),
        ]);
    }


    
}
