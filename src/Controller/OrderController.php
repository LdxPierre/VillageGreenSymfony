<?php

namespace App\Controller;

use DateTime;
use App\Entity\Order;
use App\Entity\Address;
use App\Form\OrderType;
use App\Entity\OrderItem;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use App\Repository\CartItemRepository;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_order')]
    public function index(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(['user' => $this->getUser()]);

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/new', name: 'app_order_new')]
    public function new(AddressRepository $addressRepository, Request $request, CartItemRepository $cartItemRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $order = new Order();

        // get CartItems
        $cartItems = $cartItemRepository->getItems($user);
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
            // get data from form
            $formData = $request->get('order');
            $shippingAddress = $addressRepository->findOneBy(['id' => $formData['shipping']]);

            // set status, data, shipAddress, billAddress and persist
            $order
                ->setUser($this->getUser())
                ->setStatus('En attente')
                ->setDate(new DateTime('now'))
                ->setShipping($shippingAddress);
            if (isset($formData['billingCheck'])) {
                $order->setBilling($shippingAddress);
            } else {
                $order->setBilling($addressRepository->findOneBy(['id' => $formData['billing']]));
            }
            $entityManager->persist($order);

            // new orderItems
            foreach ($cartItems as $item) {
                $orderItem = new OrderItem();
                $orderItem->setProduct($item->getProduct())
                    ->setOrderParent($order)
                    ->setPrice($item->getProduct()->getPrice())
                    ->setQuantity($item->getQuantity());
                $entityManager->persist($orderItem);
            }

            // remove cart
            $cartItemRepository->clearItems($user);
            $request->getSession()->set('cart', null);

            // flush order, orderItems, cartItems
            $entityManager->flush();

            return $this->redirectToRoute('app_user', [], Response::HTTP_FOUND);
        }

        return $this->render('order/new.html.twig', [
            'form' => $form->createView(),
            'addressForm' => $addressForm->createView(),
        ]);
    }
}
