<?php

namespace App\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use App\Entity\Camping;
use App\Entity\Objectif;
use App\Form\CampingType;
use App\Repository\CampingRepository;
use App\Repository\ObjectifRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


class CampingController extends AbstractController
{

    #[Route('/camping/{id}/objectif', name: 'app_camping_objectif', methods: ['GET'])]
    public function showObjectifsByCamping(Camping $camping): Response
    {
        $objectif = $camping->getObjectif();
    
        return $this->render('camping/objectifs.html.twig', [
            'camping' => $camping,
            'objectif' => $objectif,
        ]);
    }
    
   
#[Route('/camping', name: 'app_camping', methods: ['GET'])]
public function index(CampingRepository $campingRepository, PaginatorInterface $paginator, EntityManagerInterface $entityManager, Request $request): Response
{
    // Query campings using the repository
    $campings = $campingRepository->findAll();

    // Paginate the results
    $page = $paginator->paginate(
        $campings,
        $request->query->getInt('page', 1),
        2
    );
    // Create the form
        

    // Handle form submission

    return $this->render('camping/index.html.twig', [
        'campings' => $campings,
        'page' => $page,
    ]);}
    #[Route('/admin/camping', name: 'app_camping_back', methods: ['GET'])]
public function index2(CampingRepository $campingRepository, PaginatorInterface $paginator, EntityManagerInterface $entityManager, Request $request): Response
{
    // Query campings using the repository
    $campings = $campingRepository->findAll();

    // Paginate the results
    $page = $paginator->paginate(
        $campings,
        $request->query->getInt('page', 1),
        2
    );
    // Create the form
        

    // Handle form submission

    return $this->render('camping/index2.html.twig', [
        'campings' => $campings,
        'page' => $page,
    ]);}

    #[Route('/admin/camping/new', name: 'app_camping_new')]
    public function new(Request $request): Response
    {

        $campings = new Camping();
       // Assuming you have an instance of the Camping class


// Accessing the objid property using the getObjectif() method
$objectif = $campings->getObjectif();

// Now you can use $objectif as needed
if ($objectif !== null) {
    // Assuming getObjid() is a method in the Objectif class
    $objidValue = $objectif->getObjid();
    // Do something with $objidValue
    echo "objid value: " . $objidValue;
} else {
    // Handle the case where objid is null
    echo "objid is null";
}

        $form = $this->createForm(CampingType::class, $campings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($campings);
            $entityManager->flush();

            return $this->redirectToRoute('app_camping');
        }

        return $this->render('camping/new.html.twig', [
            'campings' => $campings,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/camping/{id}/edit', name: 'edit_camping_item')]
    public function edit(Request $request, Camping $campings): Response
    {
        $form = $this->createForm(CampingType::class, $campings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_camping');
        }

        return $this->render('camping/edit.html.twig', [
            'campings' => $campings,
            'form' => $form->createView(),
        ]);
    }

   #[Route('/admin/camping/{id}', name: 'app_camping_show', methods: ['GET'])]
public function show(Camping $camping, ObjectifRepository $objectifRepository): Response
{
    $objectif = $camping->getObjectif();

    return $this->render('camping/objectifs.html.twig', [
        'camping' => $camping,
        'objectif' => $objectif,
    ]);
}



    #[Route('/admin/camping/remove/{id}', name: 'remove_camping_item')]
    public function delete($id, EntityManagerInterface $entityManager, CampingRepository $campingRepository): Response
    {
        $campings = $campingRepository->find($id);   

        if (!$campings) {
            throw $this->createNotFoundException('Camping item not found');
        }

        $entityManager->remove($campings);          
        $entityManager->flush();

        return $this->redirectToRoute('app_camping');
    }
    #[Route('/admin/calendrier', name: 'calendrier_back_camping', methods: ['GET'])]
    public function calendrier(CampingRepository $campingRepository): Response
    {
        // Retrieve all camping entities
        $campings = $campingRepository->findAll();

        // Prepare an array to store calendar events
        $events = [];

        // Iterate over each camping entity to create calendar events
        foreach ($campings as $camping) {
            // Create an event for each camping
            $event = [
                'title' => 'camping ID :' . $camping->getId() ,
                'start' => $camping->getDatedebut()->format('Y-m-d'), // Use the date of the camping as the event start date
                'url' => $this->generateUrl('edit_camping_item', ['id' => $camping->getId()]), // Link to the camping details page
                // Add more event properties as needed
            ];

            // Add the event to the array
            $events[] = $event;
        }

        // Encode the events array to JSON for passing to the Twig template
        $data = json_encode($events);

        // Render the Twig template with the events data
        return $this->render('camping/calendrier.html.twig', compact('data'));
    }

}



