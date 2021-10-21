<?php

declare(strict_types=1);

namespace App\Controller;

use App\Doctrine\SchemaManager;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MainController extends AbstractController
{
    public function homepage(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('main/homepage.html.twig', [
            'products' => $products,
            'search' => null,
        ]);
    }

    public function search(Request $request, ProductRepository $productRepository): Response
    {
        $search = $request->query->get('searchTerm');
        $products = $productRepository->search($search);

        return $this->render('main/homepage.html.twig', [
            'products' => $products,
            'search' => $search
        ]);
    }

    public function admin(): Response
    {
        return $this->render('main/admin.html.twig');
    }

    public function dbRebuild(SchemaManager $schemaManager): Response
    {
        $schemaManager->rebuildSchema();
        $schemaManager->loadFixtures();

        return new JsonResponse(array(
            'success' => true
        ));
    }
}
