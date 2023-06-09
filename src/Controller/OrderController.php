<?php

namespace App\Controller;

use DateTime;
use App\Entity\Order;
use App\Entity\Address;
use App\Form\OrderType;
use App\Form\AddressType;
use App\Repository\OrderRepository;
use App\Repository\AddressRepository;
use App\Repository\CartItemRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/new', name: 'app_order_new')]
    public function index(AddressRepository $addressRepository, Request $request, OrderRepository $orderRepository, CartItemRepository $cartItemRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $order = new Order();

        // Add user addresses as choices
        $addresses = $addressRepository->findBy(['user' => $user]);
        if ($addresses != null) {
            foreach ($addresses as $address) {
                $options['address'][$address->__toString()] = $address->getId();
            }
        } else {
            $options['address']['Aucune adresse'] = null;
        }

        // new address form
        $address = new Address();
        $addressForm = $this->createForm(AddressType::class, $address);
        $addressForm->handleRequest($request);

        if ($addressForm->isSubmitted() && $addressForm->isValid()) {
            $address->setUser($this->getUser());
            $addressRepository->save($address, true);

            return $this->redirect($request->headers->get('referer'));
        }

        // create order form
        $form = $this->createForm(OrderType::class, $order, $options);
        $form->handleRequest($request);

        // submit order form
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $request->get('order');
            $shippingAddress = $addressRepository->findOneBy(['id' => $formData['shipping']]);

            $order->setStatus('pending')
                ->setDate(new DateTime('now'))
                ->setShipping($shippingAddress);

            // set billing address
            if (isset($formData['billingCheck'])) {
                $order->setBilling($shippingAddress);
            } else {
                $order->setBilling($addressRepository->findOneBy(['id' => $formData['billing']]));
            }

            // flush order
            $orderRepository->save($order, true);

            // remove cart
            $cartItemRepository->clearItems($user);
            $request->getSession()->set('cart', null);


            return $this->redirectToRoute('app_user', [], Response::HTTP_FOUND);
        }

        return $this->render('order/new.html.twig', [
            'form' => $form->createView(),
            'addressForm' => $addressForm->createView(),
        ]);
    }
}
