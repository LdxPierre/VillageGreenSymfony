<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/personal-information', name: 'app_user_info')]
    public function informations(): Response
    {
        return $this->render('user/info.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/edit', name: 'app_user_edit')]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        }

        return $this->render('user/edit.html.twig', [
            'user' => $this->getUser(),
            'form' => $form->createView(),
        ]);
    }
}
