<?php

namespace App\Controller;

use App\Repository\CartItemRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    public function navbar(CategoryRepository $categoryRepository, CartItemRepository $cartItemRepository, Request $request)
    {
        $user = $this->getUser();
        $session = $request->getSession();
        $visitorId = $request->cookies->get('VISID');
        $cart = [];
        $cartCount = 0;

        if ($session->get('cart') == null) {
            $cart = $cartItemRepository->getItems($user, $visitorId);
        } else {
            $cart = $session->get('cart');
        }

        foreach ($cart as $item) {
            $cartCount += $item->getQuantity();
        }

        $categories = $categoryRepository->findMainCategories();
        return $this->render('base/navbar.html.twig', [
            'categories' => $categories,
            'navCategories' => array_splice($categories, 0, 5),
            'cartCount' => $cartCount
        ]);
    }
}
