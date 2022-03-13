<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/api/v1/order/create', name: 'api_v1_order_create')]
    public function create(OrderRepository $repository, Request $request): JsonResponse
    {
        $code = $request->get('code') ?? uniqid();
        $order = (new Order())
            ->setCode($code);

        $repository->add($order);

        return $this->json([
            'result' => 'OK',
            'code' => $code
        ]);
    }
}
