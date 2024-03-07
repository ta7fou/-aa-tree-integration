<?php

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Animals;
use App\Entity\Demande;
use App\Form\AnimalType;
use App\Repository\AnimalsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnimalsController extends AbstractController
{
   

    #[Route('/animals', name: 'app_animals')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $searchQuery = $request->query->get('q');
        $orderBy = $request->query->get('orderBy', 'name'); // Default sorting by name

        $queryBuilder = $this->getDoctrine()->getRepository(Animals::class)->createQueryBuilder('a');

        // Apply search query if provided
        if ($searchQuery) {
            $queryBuilder->andWhere('a.name LIKE :searchQuery')
                ->setParameter('searchQuery', '%'.$searchQuery.'%');
        }

        // Apply sorting
        $queryBuilder->orderBy('a.'.$orderBy);

        $query = $queryBuilder->getQuery();

        $animals = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('animals/index.html.twig', [
            'pagination' => $animals,
            'searchQuery' => $searchQuery,
            'orderBy' => $orderBy,
        ]);
    }


#[Route('/animals/list', name: 'animals_list')]
public function list(): Response
{
    // Get the list of animals from the database
    $animals = $this->getDoctrine()->getRepository(Animals::class)->findAll();

    // Render the animals list template and pass the list of animals
    return $this->render('animals/list.html.twig', [
        'animals' => $animals,
    ]);
}


    #[Route('admin/animals/new', name: 'app_animals_new')]
    
    public function new(Request $request): Response
    {
        $animal = new Animals();
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($animal);
            $entityManager->flush();

            return $this->redirectToRoute('app_home_back');
        }

        return $this->render('animals/new.html.twig', [
            'animal' => $animal,
            'form' => $form->createView(),
        ]);
    }

    #[Route('animals/{id}/edit', name: 'edit_animal')]
    public function edit(Request $request, Animals $animal): Response
    {
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('animals_list');
        }

        return $this->render('animals/edit.html.twig', [
            'animal' => $animal,
            'form' => $form->createView(),
        ]);
    }

    #[Route('animals/remove/{id}', name: 'remove_animal')]
    public function delete($id, EntityManagerInterface $entityManager, AnimalsRepository $animalRepository): Response
    {
        $animal = $animalRepository->find($id);   // Retrieve the animal to be removed

        if (!$animal) {
            throw $this->createNotFoundException('Animal not found');
        }

        $entityManager->remove($animal);         // Perform the removal 
        $entityManager->flush();

        return $this->redirectToRoute('animals_list');
    }
    



}