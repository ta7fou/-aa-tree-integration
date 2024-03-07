<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\Animals;
use App\Form\DemandeType;
use App\Repository\AnimalsRepository;
use App\Repository\DemandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Mailer\MailerInterface;

class DemandeController extends AbstractController
{
    private $security;
    private $mailer;

    public function __construct(Security $security , MailerInterface $mailer)
    {
        $this->security = $security;
        $this->mailer = $mailer;
        
    }
    

   
    

   

#[Route('/demande/{animalId}', name: 'app_demande')]
public function demande(Request $request, Animals $animalId , AnimalsRepository $animalsRepository): Response
{
    $user = $this->getUser(); // Get current user

    // Check if user is authenticated
    if (!$user) {
        // Handle non-authenticated users, for example:
        throw $this->createAccessDeniedException('You must be logged in to access this page.');
    }

    // Check if user has an email
    $email = $user->getEmail();
    if ($email === null) {
        // Handle the case where user's email is null, for example:
        throw new \Exception('User email cannot be null.'); // Or handle this case differently
    }
    $animal = $animalsRepository->find($animalId);

        if (!$animal) {
            throw $this->createNotFoundException('Animal not found');
        }
    $demande = new Demande();
    $demande->setAnimalId($animalId);
    $demande->setIdUser($user->getId()); // Set id_user property
    $demande->setEmail($email); // Set email property

    // Create the form
    $form = $this->createForm(DemandeType::class, $demande);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle form submission
        // For example, persist the demande entity to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($demande);
        $entityManager->flush();

        // Redirect to a success page or do something else
        return $this->redirectToRoute('app_home');
    }

    return $this->render('demande/index.html.twig', [
        'demande' => $demande,
        'form' => $form->createView(),
        'animalId' => $animalId,
    ]);
}


    #[Route('/demandes/{userId}', name: 'demandes')]
    public function showDemandes(Request $request, DemandeRepository $demandeRepository, $userId): Response
    {
        // Fetch demandes associated with the provided user ID
        $demandes = $demandeRepository->findByUserId($userId);

        // Pass demandes and user ID to the Twig template
        return $this->render('demande/show.html.twig', [
            'demandes' => $demandes,
            'userId' => $userId,
        ]);
    }

    #[Route('/demandes/{id}/edit', name: 'edit_demande')] // the demande's id
    public function edit(Request $request, Demande $demande): Response
    {
        $demandeId = $request->query->get('id');
        $form = $this->createForm(DemandeType::class, $demande, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_animals');
        }

        return $this->render('demande/edit.html.twig', [
            'demande' => $demande,
            'form' => $form->createView(),
            'demandeId' => $demandeId,
        ]);
    }

    #[Route('/demandes/remove/{id}', name: 'remove_demande')] // the demande's id
    public function delete($id, EntityManagerInterface $entityManager, DemandeRepository $demandeRepository): Response
    {
        $animal = $demandeRepository->find($id);   // Retrieve the animal to be removed

        if (!$animal) {
            throw $this->createNotFoundException('demande not found');
        }

        $entityManager->remove($animal);         // Perform the removal 
        $entityManager->flush();

        return $this->redirectToRoute('app_animals');
    }

    #[Route('admin/demandes', name: 'demandes_list')]
    public function demandesList(DemandeRepository $demandeRepository): Response
    {
        $demandes = $demandeRepository->findAll();

        return $this->render('demande/list.html.twig', [
            'demandes' => $demandes,
        ]);
    }

    #[Route('admin/demandes/{id}/validate', name: 'validate_demande', methods: ['POST'])]
    public function validateDemande(Request $request, Demande $demande , MailerInterface $mailer): Response
    {
        //// Implement your validation logic here
    // For example, send an email to the user and remove the demande
    
    $this->sendValidationEmail($demande, $mailer);

    // Redirect back to the demandes list after validation
    return $this->redirectToRoute('demandes_list');
    }

    #[Route('admin/demandes/{id}/refuse', name: 'refuse_demande', methods: ['POST'])]
    public function refuseDemande(Request $request, Demande $demande , MailerInterface $mailer): Response
    {
        // Implement your refusal logic here
        // For example, send an email to the user and delete the demande
        $this->sendRefusalEmail($demande, $mailer);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($demande);
        $entityManager->flush();

        // Redirect back to the demandes list after refusal
        return $this->redirectToRoute('demandes_list');
    }
    
    

    private function sendValidationEmail(Demande $demande, MailerInterface $mailer): void
    {
        $email = (new Email())
            ->from('adam.somai@esprit.tn')
            ->to($demande->getEmail())
            ->subject('Demande Validation')
            ->html($this->renderView('emails/validation.html.twig', [
                'demande' => $demande,
            ]));
    
        $mailer->send($email);
    }
    private function sendRefusalEmail(Demande $demande , MailerInterface $mailer): void
    {
        $email = (new Email())
            ->from('adam.somai@esprit.tn')
            ->to($demande->getEmail())
            ->subject('Demande Validation')
            ->html($this->renderView('emails/refusal.html.twig', [
                'demande' => $demande,
            ]));
    
        $mailer->send($email);
    }
}

