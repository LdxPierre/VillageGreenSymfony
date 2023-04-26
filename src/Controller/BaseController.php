<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    public function navbar(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findMainCategories();
        return $this->render('base/navbar.html.twig', [
            'categories' => $categories,
            'navCategories' => array_splice($categories, 0, 5),
        ]);
    }
}
