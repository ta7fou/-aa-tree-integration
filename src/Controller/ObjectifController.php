<?php

namespace App\Controller;
use Symfony\Component\Filesystem\Filesystem;

use App\Entity\Objectif;
use App\Form\ObjectifType;
use App\Repository\ObjectifRepository;
use App\Repository\CampingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
class ObjectifController extends AbstractController
{
    
    #[Route('/objectif', name: 'app_objectif')]
    public function index(ObjectifRepository $objectifRepository,PaginatorInterface $paginator, EntityManagerInterface $entityManager, Request $request): Response
    {
        $objectifs = $objectifRepository->findAll();

        $page = $paginator->paginate(
            $objectifs,
            $request->query->getInt('page', 1),
            2
        );
        return $this->render('objectif/index.html.twig', [
            'objectifs' => $objectifs,
            'page' => $page,
        ]);
    }
  

    #[Route('/objectif/new', name: 'app_objectif_new')]
    public function new(Request $request): Response
    {
        $objectifs = new Objectif();
        $form = $this->createForm(ObjectifType::class, $objectifs);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageobj = $form->get('imageobj')->getData();

            if ($imageobj) {
                $newFilename = uniqid().'.'.$imageobj->guessExtension();
                $imageobj->move(
                    $this->getParameter('upload_directory'),
                    $newFilename
                );
                $objectifs->setImageobj($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($objectifs);
            $entityManager->flush();

            return $this->redirectToRoute('app_objectif');
        }

        return $this->render('objectif/new.html.twig', [
            'objectifs' => $objectifs,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/objectif/edit/{id}', name: 'edit_objectif_item')]
    public function edit(Request $request, Objectif $objectifs): Response
    {
        $form = $this->createForm(ObjectifType::class, $objectifs);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageobj = $form->get('imageobj')->getData();

            if ($imageobj) {
                // Delete the existing image
                $existingImage = $objectifs->getImageobj();
                if ($existingImage) {
                    $filesystem = new Filesystem();
                    $filesystem->remove($this->getParameter('upload_directory').'/'.$existingImage);
                }
                $newFilename = uniqid().'.'.$imageobj->guessExtension();
                $imageobj->move(
                    $this->getParameter('upload_directory'),
                    $newFilename
                );
                $objectifs->setImageobj($newFilename);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_objectif');
        }

        return $this->render('objectif/edit.html.twig', [
            'objectifs' => $objectifs,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/objectif/remove/{id}', name: 'remove_objectif_item')]
    public function delete($id, EntityManagerInterface $entityManager, ObjectifRepository $objectifRepository): Response
    {
        $objectifs = $objectifRepository->find($id);   

        if (!$objectifs) {
            throw $this->createNotFoundException('Objectif item not found');
        }

        $entityManager->remove($objectifs);        
        $entityManager->flush();

        return $this->redirectToRoute('app_objectif');
    }


}

