<?php

namespace App\Controller;

use DateTime;
use App\Entity\Order;
use App\Entity\Address;
use App\Form\OrderType;
use App\Entity\OrderItem;
use App\Form\AddressType;
use App\Repository\OrderRepository;
use App\Repository\AddressRepository;
use App\Repository\CartItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_order')]
    public function index(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(['user' => $this->getUser()], ['date' => 'DESC']);

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/new', name: 'app_order_new')]
    public function new(AddressRepository $addressRepository, Request $request, CartItemRepository $cartItemRepository, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        /** @var User */
        $user = $this->getUser();
        $order = new Order();

        // get CartItems
        $cartItems = $cartItemRepository->getItems($user);

        // Redirect if empty cart
        if (!$cartItems[0]) {
            return $this->redirectToRoute('app_cart');
        }

        // Add user addresses as choices
        $addresses = $addressRepository->findBy(['user' => $user]);
        if ($addresses != null) {
            foreach ($addresses as $address) {
                $options['address'][$address->__toString()] = $address->getId();
            }
        } else {
            $options['address']['Aucune adresse'] = null;
        }
        // Total
        foreach ($cartItems as $item) {
            $price[] = $item->getTotal();
        }
        $total = array_sum($price);

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

            // send confirmation email
            $email = (new TemplatedEmail())
                ->from('noreply@village-green.com')
                ->to($user->getEmail())
                ->subject('Votre commande a été enregistrée')
                ->htmlTemplate('order/confirmation_email.html.twig')
                ->context(['order' => $order]);

            $mailer->send($email);

            return $this->redirectToRoute('app_order_success', [], Response::HTTP_FOUND);
        }

        return $this->render('order/new.html.twig', [
            'form' => $form->createView(),
            'addressForm' => $addressForm->createView(),
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    #[Route('/success', name: 'app_order_success')]
    function success(OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->lastOrder($this->getUser());

        return $this->render('order/success.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{id}', name: 'app_order_show')]
    function show(Order $order): Response
    {
        // Redirect if not order owner
        if ($this->getUser() != $order->getUser()) {
            return $this->redirectToRoute('app_order');
        }

        return $this->render('order/show.html.twig', [
            'order' => $order,
            'total' => $order->getTotal(),
        ]);
    }
}
