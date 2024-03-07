<?php

namespace App\Controller;

use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
{
    // Pass any data you need to the template
    $data = [
        'message' => 'Welcome to your new controller!',
    ];

    return $this->render('base1.html.twig', $data);
} 
#[Route('/home2', name: 'app_home2')]
public function index2(): Response
{
// Pass any data you need to the template
$data = [
    'message' => 'Welcome to your new controller!',
];

return $this->render('base.html.twig', $data);
}

#[Route('/admin/back', name: 'app_home_back')]
    public function back(): Response
{
    // Pass any data you need to the template
    $data = [
        'message' => 'Welcome to your new controller!',
    ];

    return $this->render('baseBack.html.twig', $data);
}

#[Route('/admin/profile', name: 'app_profile_back')]
    public function backProfile(Request $request,UserPasswordHasherInterface $userPasswordHasher,EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();
    $form = $this->createForm(ProfileFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $form->get('Password')->getData()
            )
        );
        $entityManager->flush();

        return $this->redirectToRoute('app_home_back', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('profile/edit.html.twig', [
        'user' => $user,
        'form' => $form,
    ]);
}


}
