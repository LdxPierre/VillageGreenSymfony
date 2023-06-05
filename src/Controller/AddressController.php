<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/address')]
class AddressController extends AbstractController
{
    #[Route('/new', name: 'app_address_new', methods: ['GET', 'POST'])]
    public function newAddress(Request $request, AddressRepository $addressRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $address = new Address();
        $user = $this->getUser();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($user);
            $addressRepository->save($address, true);

            return $this->redirectToRoute('app_user_info');
        }

        return $this->render('address/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_address_edit', methods: ['GET', 'POST'])]
    public function editAddress(Request $request, Address $address, AddressRepository $addressRepository): Response
    {
        // Redirect if not the address owner
        if ($address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_user_info');
        }

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $addressRepository->save($address, true);

            return $this->redirectToRoute('app_user_info');
        }

        return $this->render('address/edit.html.twig', [
            'form' => $form->createView(),
            'edit' => true,
            'address' => $address
        ]);
    }

    #[Route('/delete/{id}', name: 'app_address_delete', methods: ['POST'])]
    public function deleteAddress(Address $address, Request $request, AddressRepository $addressRepository): Response
    {
        // Redirect if not the address owner
        if ($address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_user_info');
        }

        if ($this->isCsrfTokenValid('delete' . $address->getId(), $request->request->get('_token'))) {
            $addressRepository->remove($address, true);
        }
        return $this->redirectToRoute('app_user_info');
    }
}
