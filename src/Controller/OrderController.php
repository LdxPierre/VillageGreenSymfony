<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\AddressRepository;
use App\Repository\CartItemRepository;
use App\Repository\OrderRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/new', name: 'app_order_new')]
    public function index(AddressRepository $addressRepository, Request $request, OrderRepository $orderRepository, CartItemRepository $cartItemRepository): Response
    {
        $user = $this->getUser();
        $order = new Order();

        // Add user addresses as choices
        $addresses = $addressRepository->findBy(['user' => $user]);
        foreach ($addresses as $address) {
            $options['address'][$address->__toString()] = $address->getId();
        }

        // create form
        $form = $this->createForm(OrderType::class, $order, $options);
        $form->handleRequest($request);

        // submit form
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $request->get('order');
            $shippingAddress = $addressRepository->findOneBy(['id' => $formData['shipping']]);

            $order->setStatus('pending')
                ->setDate(new DateTime('now'))
                ->setShipping($shippingAddress);

            // set billing address
            if (isset($formData['billing'])) {
                $order->setBilling($addressRepository->findOneBy(['id' => $formData['billing']]));
            } else {
                $order->setBilling($shippingAddress);
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
        ]);
    }
}
