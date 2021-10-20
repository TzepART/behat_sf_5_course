<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepageAction()
    {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->search('');

        return $this->render('main/homepage.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/search", name="product_search")
     */
    public function searchAction(Request $request)
    {
        $search = $request->query->get('searchTerm');

        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->search($search);

        return $this->render('main/homepage.html.twig', [
            'products' => $products,
            'search' => $search
        ]);
    }


    /**
     * @Route("/admin", name="admin")
     */
    public function adminAction()
    {
        return $this->render('main/admin.html.twig');
    }

    /**
     * @Route("/_db/rebuild", name="db_rebuild")
     */
    public function dbRebuildAction()
    {
        $schemaManager = $this->get('schema_manager');
        $schemaManager->rebuildSchema();
        $schemaManager->loadFixtures();

        return new JsonResponse(array(
            'success' => true
        ));
    }
}
