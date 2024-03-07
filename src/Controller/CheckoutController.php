<?php

namespace App\Controller;
use App\Entity\Facture;
use App\Entity\User;
use App\Repository\ProduitRepository;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;


class CheckoutController extends AbstractController
{
    #[Route('/admin/listfac$facture', name: 'app_checkliste')]
    public function index(): Response
    {
        $facture = $this->getDoctrine()->getRepository(facture::class)->findAll();
        return $this->render('checkout/show.html.twig', [
            'facture' => $facture,
        ]);
    }
    #[Route('/checkout', name: 'app_checkout')]
    public function new(Request $request, EntityManagerInterface $entityManager, ProduitRepository $produitRepository, SessionInterface $session, Security $security): Response
    {
        $user = $security->getUser();
    
        // Create a new Facture instance
        $facture = new facture();
    
        // Check if the user is authenticated
        if ($user !== null) {
            // If the user is authenticated, populate the Facture with user information
            $facture->setUser($user)
                    ->setName($user->getNom())
                    ->setEmail($user->getEmail())
                    ->setAddress($user->getAddress());
        }
    
        // Calculate total amount based on the cart
        $cart = $session->get('cart', []);
        $produitIds = array_keys($cart);
        $produits = $produitRepository->findBy(['id' => $produitIds]);
        $totalAmount = 0;
        foreach ($produits as $produit) {
            $quantity = $cart[$produit->getId()];
            $totalAmount += $produit->getPriu() * $quantity;
        }
    
        // Set the total amount in the Facture
        $facture->setPrixtotal($totalAmount); 
    
        // Create the form
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);
    
        // Handle form submission
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($facture);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_home');
        }
    
        // Render the template
        return $this->render('checkout/index.html.twig', [
            'facture' => $facture,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('admin/editc/{id}', name: 'edit_facture')]
    public function edit(Request $request, Facture $facture): Response
    {
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('checkout/edit.html.twig', [
            'facture' => $facture,
            'form' => $form->createView(),
        ]);
    }
    #[Route('admin/remove/{id}', name: 'remove_facture')]
    public function delete($id, EntityManagerInterface $entityManager, FactureRepository $factureRepository): Response
    {
        $facture = $factureRepository->find($id);   // Retrieve the animal to be removed

        if (!$facture) {
            throw $this->createNotFoundException('facture not found');
        }

        $entityManager->remove($facture);         // Perform the removal 
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }
}
