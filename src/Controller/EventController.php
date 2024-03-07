<?php 
namespace App\Controller;
use App\Entity\Campagne;
use App\Repository\EventRepository;
use App\Entity\Event;
use App\Entity\Participation;
use App\Form\EventType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    #[Route('/event', name: 'app_event')]
    public function index(EventRepository $eventRepository): Response
    {
        // Récupérer la liste des événements depuis la base de données
        $events = $eventRepository->findAll();

        // Rendre le template et passer la liste des événements
        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }
    #[Route('/event/new', name: 'event_new')]
    public function new(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_campaign'); // Redirection vers la liste des campagnes
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/event/{id}/edit', name: 'app_event_edit')]
    public function edit(Request $request, Event $event): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_event');
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }
/*
    #[Route('/event/{id}', name: 'app_event_show')]
public function show(Event $event, Request $request, Campagne $camp ): Response
{
    $user = $this->getUser(); // Get current user

    // Create a new instance of Participation
    $participation = new Participation();
    $participation->setUserId($user->getId());
    $participation->setEventId($event->getId());
    $participation->setCampId($camp->getId());



    // Create the form
    $form = $this->createFormBuilder($participation)
        ->add('nom')
        ->add('prenom')
        ->add('email')
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Save the participation entity to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($participation);
        $entityManager->flush();

        // Redirect to a success page or do something else
        return $this->redirectToRoute('app_home');
    }

    return $this->render('participation/form.html.twig', [
        'form' => $form->createView(),
        'event' => $event,
    ]);
}*/

    #[Route('/event/{id}/delete', name: 'app_event_delete')]
    public function delete(Request $request, Event $event): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event');
    }
}
