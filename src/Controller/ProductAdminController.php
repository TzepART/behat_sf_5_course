<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ProductAdminController extends AbstractController
{
    public function list(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('product/list.html.twig', [
            'products' => $products
        ]);
    }

    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $this->addFlash('success', 'Product created FTW!');

            $product = (new Product())
                ->setName($request->get('name'))
                ->setDescription($request->get('description'))
                ->setPrice((int) $request->get('price'))
                ->setAuthor($this->getUser());

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_list');
        }

        return $this->render('product/new.html.twig');
    }

    /**
     * @ParamConverter("product", class="App\Entity\Product")
     */
    public function delete(Product $product, EntityManagerInterface $em): Response
    {
        $em->remove($product);
        $em->flush();

        $this->addFlash('success', 'The product was deleted');

        return $this->redirectToRoute('product_list');
    }
}
