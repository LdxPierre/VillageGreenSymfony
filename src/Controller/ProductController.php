<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/{url}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        // generate links for breadcrumb
        $categoryTree = $product->getCategory()->getCategoryTree();
        array_push($categoryTree, $product->getCategory());

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'breadcrumbLinks' => $categoryTree
        ]);
    }
}
