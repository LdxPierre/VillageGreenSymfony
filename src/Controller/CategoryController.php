<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findMainCategories(),
        ]);
    }

    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{url}', name: 'app_category_show', methods: ['GET', 'POST'])]
    public function show(Category $category, Request $request, ProductRepository $productRepository): Response
    {
        // return child categories if exists
        $childCategories = $category->getChildren();
        if ($childCategories[0]) {
            return $this->render('category/show.html.twig', [
                'category' => $category,
                'childCategories' => $childCategories,
                'breadcrumbLinks' => $category->getCategoryTree(),
            ]);
        }

        // render filtered products if filter is on
        if ($request->query->get('filter') == 'on') {
            // get all params
            $params = $request->query->all();
            // array of filters
            $filters['brands'] = [];
            $filters['categoryId'] = $category->getId();
            // stock all brands checked in filters['brands']
            foreach ($params as $key => $value) {
                if (strstr($key, 'brand_')) {
                    array_push($filters['brands'], $value);
                }
            }
            // assign min & max price with default = null
            $filters['min'] = $params['min'] ?? null;
            $filters['max'] = $params['max'] ?? null;
            $filters['sort'] = $params['sort'];

            return $this->render('category/show.html.twig', [
                'category' => $category,
                'products' => $productRepository->filter($filters),
                'brands' => $productRepository->fetchUniqueBrands($category->getId()),
                'filters' => $filters,
                'breadcrumbLinks' => $category->getCategoryTree(),
            ]);
        }

        // render all products
        $products = $category->getProducts();
        return $this->render('category/show.html.twig', [
            'category' => $category,
            'products' => $products,
            'brands' => $productRepository->fetchUniqueBrands($category->getId()),
            'breadcrumbLinks' => $category->getCategoryTree(),
        ]);
    }

    #[Route('/{url}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
            'breadcrumbLinks' => $category->getCategoryTree(),
        ]);
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);
        }

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
