<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Repository\CartItemRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart')]
    public function index(CartItemRepository $cartItemRepository, Request $request): Response
    {
        // get user Object
        $user = $this->getUser();
        // get session Object
        $session = $request->getSession();
        // fetch cart from database if logged else from session
        $cart = $cartItemRepository->getItems($user, $session);
        $session->set('cart', $cart);

        if ($cart != null) {
            foreach ($cart as $item) {
                $price[] = $item->getQuantity() * $item->getProduct()->getPrice();
            }
            $total = array_sum($price);
        } else {
            $total = 0;
        }

        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'cart' => $cart,
            'total' => $total,
        ]);
    }

    #[Route('/add', name: 'app_cart_add_item', methods: ['POST'])]
    public function add(ProductRepository $productRepository, Request $request, CartItemRepository $cartItemRepository): Response
    {
        $user = $this->getUser();
        $session = $request->getSession();
        $session->start();
        $product = $productRepository->findOneBy(['id' => $request->get('id')]);

        $item = new CartItem();
        $item->setProduct($product);
        $item->setQuantity($request->get('quantity'));
        if ($user != null) {
            $oldItem = $cartItemRepository->findOneBy(['product' => $product, 'user' => $user]);
            if ($oldItem) {
                $oldItem->setQuantity($oldItem->getQuantity() + $item->getQuantity());
                $cartItemRepository->save($oldItem, true);
            } else {
                $item->setUser($user);
                $cartItemRepository->save($item, true);
            }
            $cart = $cartItemRepository->findBy(['user' => $user]);
        } else {
            $oldItem = $cartItemRepository->findOneBy(['product' => $product, 'sessionId' => $session->getId()]);
            if ($oldItem) {
                $oldItem->setQuantity($oldItem->getQuantity() + $item->getQuantity());
                $cartItemRepository->save($oldItem, true);
            } else {
                $item->setSessionId($session->getId());
                $cartItemRepository->save($item, true);
            }
            $cart = $cartItemRepository->findBy(['sessionId' => $session->getId()]);
        }

        // update cart in $_SESSION
        $session->set('cart', $cart);

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/{id}', name: 'app_cart_item_delete', methods: ['POST'])]
    public function delete(Request $request, CartItem $cartItem, CartItemRepository $cartItemRepository): Response
    {
        $session = $request->getSession();
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('delete' . $cartItem->getId(), $request->request->get('_token'))) {
            $cartItemRepository->remove($cartItem, true);
        }

        // update cart in $_SESSION
        $cart = $cartItemRepository->getItems($user, $session);
        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);
    }
}
