<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Repository\ProductRepository;
use App\Repository\CartItemRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart')]
    public function index(CartItemRepository $cartItemRepository, Request $request): Response
    {
        // User
        $user = $this->getUser();
        // VisitorId
        $visitorId = $request->cookies->get('VISID');
        // fetch cart from database if logged or with visitorId
        $cart = $cartItemRepository->getItems($user, $visitorId);
        // Refresh cart in session
        $request->getSession()->set('cart', $cart);

        if ($cart != null) {
            foreach ($cart as $item) {
                $price[] = $item->getTotal();
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
        // Response
        $response = new RedirectResponse($request->headers->get('referer'), Response::HTTP_FOUND);
        // User
        $user = $this->getUser();
        // VisitorId
        $visitorId = $request->cookies->get('VISID');
        if ($visitorId == null && $user == null) {
            $visitorId = uniqid();
            $cookie = new Cookie('VISID', $visitorId, strtotime('+1 year'));
            $response->headers->setCookie($cookie);
        }
        // Product
        $product = $productRepository->findOneBy(['id' => $request->get('id')]);

        // New cartItem
        $item = new CartItem();
        $item->setProduct($product);
        $item->setQuantity($request->get('quantity'));

        // save new items
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
            $oldItem = $cartItemRepository->findOneBy(['product' => $product, 'visitorId' => $visitorId]);
            if ($oldItem) {
                $oldItem->setQuantity($oldItem->getQuantity() + $item->getQuantity());
                $cartItemRepository->save($oldItem, true);
            } else {
                $item->setVisitorId($visitorId);
                $cartItemRepository->save($item, true);
            }
            $cart = $cartItemRepository->findBy(['visitorId' => $visitorId]);
        }

        // update cart in $_SESSION
        $request->getSession()->set('cart', $cart);

        return $response;
    }

    #[Route('/{id}', name: 'app_cart_item_delete', methods: ['POST'])]
    public function delete(Request $request, CartItem $cartItem, CartItemRepository $cartItemRepository): Response
    {
        // User
        $user = $this->getUser();
        // Visitor Id
        $visitorId = $request->cookies->get('VISID');

        // Delete
        if ($this->isCsrfTokenValid('delete' . $cartItem->getId(), $request->request->get('_token'))) {
            $cartItemRepository->remove($cartItem, true);
        }

        // update cart in $_SESSION
        $cart = $cartItemRepository->getItems($user, $visitorId);
        $request->getSession()->set('cart', $cart);

        return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);
    }
}
