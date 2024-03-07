<?php

namespace App\Controller;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class ProduitController extends AbstractController
{
    #[Route('/shop', name: 'app_produit')]
    public function index(ProduitRepository $produitRepository): Response
    {
        $produit = $this->getDoctrine()->getRepository(produit::class)->findAll();
        
        return $this->render('produit/index.html.twig', [
            'produit' => $produit,
            
        ]);
    }
    #[Route('/admin/listeproduct', name: 'app_back_product')]
    public function index2(ProduitRepository $produitRepository): Response
    {
        $produit = $this->getDoctrine()->getRepository(produit::class)->findAll();
        
        return $this->render('produit/index2.html.twig', [
            'produit' => $produit,
            
        ]);
    }
    
    #[Route('/admin/newproduct', name: 'app_produit_new')]
    
    public function new(Request $request): Response
    {
        $produit = new produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_new');
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/editp/{id}', name: 'edit_produit')]
    public function edit(Request $request, Produit $produit): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_produit');
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/admin/remove/{id}', name: 'remove_produit')]
    public function delete($id, EntityManagerInterface $entityManager, ProduitRepository $produitRepository): Response
    {
        $produit = $produitRepository->find($id);   // Retrieve the animal to be removed

        if (!$produit) {
            throw $this->createNotFoundException('product not found');
        }

        $entityManager->remove($produit);         // Perform the removal 
        $entityManager->flush();

        return $this->redirectToRoute('app_produit');
    }
    
    
    #[Route('/reserch', name: 'app_reserch')]
    public function reserch(Request $request, ProduitRepository $produitRepository): Response
{
    $searchQuery = $request->query->get('q');
    $order = $request->query->get('order', 'asc');

    // Fetch products based on search query
    $produits = [];
    if ($searchQuery) {
        $produits = $produitRepository->findBySearchQuery($searchQuery);
    } else {
        // If no search query provided, fetch all products
        $produits = $produitRepository->findAll();
    }

    // Sort products by price
    if ($order === 'asc') {
        usort($produits, function($a, $b) {
            return $a->getPriu() - $b->getPriu();
        });
    } elseif ($order === 'desc') {
        usort($produits, function($a, $b) {
            return $b->getPriu() - $a->getPriu();
        });
    }

    return $this->render('produit/index.html.twig', [
        'produit' => $produits,
        'searchQuery' => $searchQuery,
    ]);
}
   

    // Your other controller methods...
}


