<?php

namespace App\Controller;
use App\Repository\ParticipationRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Security\Core\Security;
use App\Entity\Participation;
use App\Entity\Event;
use App\Entity\Campagne;
use App\Entity\Dash;
use App\Form\ParticipationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipationController extends AbstractController
{
    #[Route('/event/{id}', name: 'app_event_show')]
public function index(Request $request, Security $security, $id): Response
{
    $user = $security->getUser();
    $entityManager = $this->getDoctrine()->getManager();

    $eventRepository = $entityManager->getRepository(Event::class);
    $event = $eventRepository->find($id);
    if (!$event) {
        throw $this->createNotFoundException('Événement non trouvé');
    }

    $campagne = $event->getCampagne();
    if (!$campagne) {
        throw $this->createNotFoundException('Campagne non trouvée');
    }

    // Récupérer camp_id de l'événement
    $camp_id = $campagne->getId();

    // Mettre à jour les statistiques de la campagne dans l'entité Dash
    $dashRepository = $entityManager->getRepository(Dash::class);
    $dash = $dashRepository->findOneBy(['campagne_id' => $camp_id]);
    if (!$dash) {
        $dash = new Dash();
        $dash->setCampagneId($camp_id);
        $dash->setNbVisites(0);
        $dash->setNbParticipations(0);
    }

    $participationRepository = $entityManager->getRepository(Participation::class);
    $participation = $participationRepository->findOneBy([
        'user_id' => $user->getId(),
        'event_id' => $id,
    ]);
    
    if (!$participation) {
        $participation = new Participation();
        $participation->setNom($user->getNom());
        $participation->setPrenom($user->getPrenom());
        $participation->setEmail($user->getEmail());
        $participation->setUserId($user->getId());
        $participation->setEventId($id);
        $participation->setCampId($camp_id);
    }
    
    $form = $this->createForm(ParticipationType::class, $participation);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        // Mettre à jour les statistiques de la campagne dans l'entité Dash
        $dash->setNbParticipations($dash->getNbParticipations() + 1);
        $entityManager->persist($participation);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_campaign');
    } else {
        // Incrémenter le nombre de visites seulement si le formulaire n'a pas été soumis
        $dash->setNbVisites($dash->getNbVisites() + 1);
    }
       $entityManager->persist($dash);
    $entityManager->flush();

    return $this->render('participation/form.html.twig', [
        'form' => $form->createView(),
    ]);
}
private $participationRepository;

public function __construct(ParticipationRepository $participationRepository)
{
    $this->participationRepository = $participationRepository;
}public function generatePdf(): Response
{
    $user = $this->getUser();
    $participations = $this->participationRepository->findBy(['user_id' => $user->getId()]);

    $dompdf = new Dompdf();
    $options = new Options();
    $dompdf->setOptions($options);

    // Récupérer le nom de la campagne à partir de camp_id
    // Récupérer le nom de l'événement à partir de event_id
    foreach ($participations as $participation) {
        $campagneRepository = $this->getDoctrine()->getRepository(Campagne::class);
        $campagne = $campagneRepository->find($participation->getCampId());
        $campagneName = $campagne ? $campagne->getNomCampagne() : 'N/A';
        $participation->campagneName = $campagneName;

        $eventRepository = $this->getDoctrine()->getRepository(Event::class);
        $event = $eventRepository->find($participation->getEventId());
        $eventName = $event ? $event->getLieu() : 'N/A';
        $participation->eventName = $eventName;
    }

    $html = $this->renderView('pdf/participation_pdf.html.twig', [
        'participations' => $participations,
    ]);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $response = new Response($dompdf->output());
    $response->headers->set('Content-Type', 'application/pdf');
    $response->headers->set('Content-Disposition', 'inline; filename="participations.pdf"');

    return $response;
}
}