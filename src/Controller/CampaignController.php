<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Campagne;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Form\CampagneType;
use App\Repository\CampagneRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EventRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampaignController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/campaign/{id}/events', name: 'app_campaign_events')]
    public function showEventsByCampagne(Campagne $campagne, EventRepository $evenementRepository): Response
    {
        // Récupérer la liste des événements pour cette campagne spécifique
        $events = $evenementRepository->findByCampagne($campagne);
    
        // Passer la liste des événements à la vue
        return $this->render('event/list.html.twig', [
            'campagne' => $campagne,
            'events' => $events,
        ]);
    }
    #[Route('/campaign/{id}/evenement', name: 'app_campaign_evenement')]
    public function showEventsByCampagnes(Campagne $campagne, EventRepository $evenementRepository): Response
    {
        // Récupérer la liste des événements pour cette campagne spécifique
        $events = $evenementRepository->findByCampagne($campagne);
    
        // Passer la liste des événements à la vue
        return $this->render('event/index.html.twig', [
            'campagne' => $campagne,
            'events' => $events,
        ]);
    }
    
    #[Route('/campaign', name: 'app_campaign')]
    public function index(CampagneRepository $campagneRepository): Response
    {
        // Get the list of campaigns from the database
        $campagnes = $campagneRepository->findAll();
    
        // Render the template and pass the list of campaigns
        return $this->render('campaign/index.html.twig', [
            'campagnes' => $campagnes,
            'currentUser' => $this->getUser(),
        ]);
    }
    
   
    #[Route('/campaign/mescampaign', name: 'app_mescampaign')]
    public function index1(CampagneRepository $campagneRepository): Response
    {
        // Récupérer l'ID de l'utilisateur connecté
        $userId = $this->getUser()->getId();
    
        // Récupérer les campagnes de l'utilisateur connecté en utilisant findByUserId
        $campagnes = $campagneRepository->findByUserId($userId);
    
        // Rendre le template et passer la liste des campagnes
        return $this->render('campaign/mescampaigns.html.twig', [
            'campagnes' => $campagnes,
            'currentUser' => $this->getUser(),
        ]);
    }
    
    

 
    
 
    #[Route('/campaign/new', name: 'app_campaign_new')]
    public function new(Request $request): Response
    {
        $user = $this->security->getUser();

        $campagne = new Campagne();
        $form = $this->createForm(CampagneType::class, $campagne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload de l'image
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // Générez un nom de fichier unique pour éviter les conflits
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                try {
                    // Déplacez le fichier téléchargé vers le répertoire de stockage des images
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    // Mettez à jour le champ 'image' de l'entité Campagne avec le nom du fichier
                    $campagne->setImage($newFilename);
                } catch (FileException $e) {
                    // Gérez l'exception si l'upload échoue
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image.');
                    return $this->redirectToRoute('app_campaign_new');
                }
            }

            // Ajoutez l'ID de l'utilisateur connecté à la campagne
            $campagne->setUserId($user->getId());

            // Enregistrez la campagne dans la base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($campagne);
            $entityManager->flush();

            $this->addFlash('success', 'La campagne a été créée avec succès.');
            return $this->redirectToRoute('event_new');
        }

        return $this->render('campaign/newc.html.twig', [
            'campagne' => $campagne,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/campaign/{id}/edit', name: 'app_campaign_edit')]
    public function edit(Request $request, Campagne $campagne): Response
    {
        $form = $this->createForm(CampagneType::class, $campagne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrez les modifications dans la base de données
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'La campagne a été mise à jour avec succès.');
            return $this->redirectToRoute('app_mescampaign', ['id' => $campagne->getId()]);
        }

        return $this->render('campaign/edit.html.twig', [
            'campagne' => $campagne,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/campaign/remove/{id}', name: 'remove_campaign')]
    public function deleteCampaign($id, EntityManagerInterface $entityManager, CampagneRepository $campagneRepository, EventRepository $eventRepository): Response
    {
        $campagne = $campagneRepository->find($id);

        if (!$campagne) {
            throw $this->createNotFoundException('Campagne not found');
        }

        // Supprimer tous les événements liés à la campagne
        $events = $eventRepository->findBy(['campagne' => $campagne]);
        foreach ($events as $event) {
            
            $entityManager->remove($event);
        }

        // Supprimer la campagne
        $entityManager->remove($campagne);
        $entityManager->flush();

        return $this->redirectToRoute('app_campaign');
    }
}